<?php
/**
 * Created by PhpStorm.
 * User: Pash
 * Date: 30.09.2015
 * Time: 8:25
 */

namespace modules\block\models;

use core\App;
use core\Obj;

class Model extends Obj
{
    public $id;
    protected static $table;

    public static function findById($value, $throw = false)
    {
        $result = App::instance()->db->from(static::$table)->where('id', $value)->execute()->fetchObject(get_called_class());
        if ($throw && !$result) throw new \Exception("Запись [$value] не найдена");
        return $result;
    }
    public static function findAll()
    {
        return App::instance()->db->from(static::$table)->execute()->fetchAll(\PDO::FETCH_CLASS, get_called_class());
    }
    public static function fromArray(array $data)
    {
        $model = new static;
        $r = new \ReflectionClass($model);
        foreach($r->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $model->{$prop->name} = array_key_exists($prop->name, $data) ? $data[$prop->name] : null;
        }
        return $model;
    }
    public function save()
    {
        $this->beforeSave();
        $data = $this->asArray();
        if ($this->id) {
            unset($data['id']);
            $this->app->db->update(static::$table, $data, $this->id)->execute();
        } else {
            $this->id = $this->app->db->insertInto(static::$table, $data)->execute();
        }
    }
    public function delete()
    {
        return $this->app->db->deleteFrom(static::$table, $this->id)->execute();
    }
    public function asArray()
    {
        return $this->getPublicProperties();
    }
    protected function getPublicProperties()
    {
        $props = [];
        $r = new \ReflectionClass($this);
        foreach($r->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $props[$prop->getName()] = $prop->getValue($this);
        }
        return $props;
    }
    public function fill(array $data)
    {
        $data = $this->beforeFill($data);
        foreach ($this->getPublicProperties() as $name => $value) {
            if (array_key_exists($name, $data)) {
                $this->$name = $data[$name];
            }
        }
    }
    protected function beforeSave(){}
    protected function beforeFill($data)
    {
        return $data;
    }
    public function __get($name)
    {
        $method = '__get' . str_replace('_', '', $name);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        } else return null;
    }
}