<?php

namespace core;

use Closure;
use core\helpers\ArrayToXml;
use Countable;
use ArrayIterator;
use JsonSerializable;
use IteratorAggregate;

class Collection implements Countable, IteratorAggregate, JsonSerializable
{
    protected $items;

    /**
     * Create a collection from passed items
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }


    private static $getters = [];

    public function set($key, $value)
    {
        $value = $this->applySetter($key, $value);
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * Return udnerlying array in collection
     * @return array
     */
    public function all()
    {
        return $this->items;
    }


    public function get($key, $default = null)
    {
        $value = (isset($this->items[$key])) ? $this->items[$key] : $default;
        return $value;
    }

    public function replace(array $items)
    {
        $this->items = $items;
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

    public function asArray()
    {
        return json_decode(json_encode($this->items), true);
    }

    public function keys()
    {
        return array_keys($this->asArray());
    }


    public function remove($key)
    {
        unset($this->items[$key]);
    }

    public function clear()
    {
        $this->items = [];
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    public function __isset($key)
    {
        $value = $this->get($key);
        return isset($value);
    }

    public static function create(array $data = [])
    {
        return static::instance()->replace($data);
    }

    public static function instance()
    {
        return new static;
    }

    private function applySetter($key, $value)
    {
        /*
         * $method = "__set" . str_replace("_", "", $key); //не понял пичину замены - удалил!
         * прична такая, пример: хотим сделать метод для установки имя юзера большими буквами это свойство в классе называется user_name
         * добавляем в класс метод будет называться __setUser_Name($value) {return strtoupper($value)}
         * но это не очень красиво, поэтому мы делаем в названии метода замену и получается __setUserName - так лучше имхо)
         *
         */
        $method = "__set" . $key;
        if (method_exists($this, $method)) $value = $this->$method($value);
        return $value;
    }

    protected function applyGetter($key, $value)
    {
        $method = "__get" . str_replace("_", "", $key);
        if (method_exists($this, $method)) $value = $this->$method($value);
        if (!empty(self::$getters[get_called_class()][$key]) && is_array(self::$getters[get_called_class()][$key])) {
            foreach (self::$getters[get_called_class()][$key] as $callable) {
                if (is_callable($callable)) {
                    $value = $callable($value, $this);
                }
            }
        }
        return $value;
    }

    public static function addGetter($key, $callable)
    {
        if (!is_callable($callable)) throw new \Exception("Second argument must be callable");
        if (!isset(self::$getters[get_called_class()][$key])) {
            self::$getters[get_called_class()] = [];
            self::$getters[get_called_class()][$key] = [];
        }
        self::$getters[get_called_class()][$key][] = $callable;
    }

    /**
     * Check if key exists in the collection
     * @param  mixed $key
     * @return boolean
     */
    public function has($key)
    {
        if (isset($this->items[$key])) {
            return true;
        }

        return false;
    }

    /**
     * Set a key and value to the collection
     * @param  mixed $key
     * @param  mixed $value
     */
    public function put($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * Add item to the end of the collection
     * @param  mixed $value
     */
    public function push($value)
    {
        $this->items[] = $value;
    }

    /**
     * Remove and return an item via its key
     * @param  mixed $key
     * @param  mixed $default
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        $value = $this->get($key, $default);

        $this->remove($key);

        return $value;
    }

    /**
     * Return the first item in the collection
     * @return mixed
     */
    public function first()
    {
        return count($this->items) ? reset($this->items) : null;
    }

    /**
     * Return the last item in the collection
     * @return mixed
     */
    public function last()
    {
        return count($this->items) ? end($this->items) : null;
    }

    /**
     * Return and remove the last item in the array
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * Return and remove the first item in the array
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }


    /**
     * Reset the collections keys
     */
    public function values()
    {
        $this->items = array_values($this->items);

        return $this;
    }

    /**
     * Transform the current collection
     * @param  Closure $iterator
     */
    public function transform(Closure $iterator)
    {
        $this->items = array_map($iterator, $this->items);

        return $this;
    }

    /**
     * Returns a collection without duplicate values
     * @return Collection
     */
    public function unique()
    {
        return new static(array_unique($this->items, SORT_REGULAR));
    }

    /**
     * Return a new collection with the items reverese
     * @return Collection
     */
    public function reverse()
    {
        return new static(array_reverse($this->items));
    }

    /**
     * Return a new collection with the items shuffled
     * @return Collection
     */
    public function shuffle()
    {
        $items = $this->items;

        shuffle($items);

        return new static($items);
    }

    /**
     * Get one or more items randomly from the collection
     * @param  integer $amount
     */
    public function random($amount = 1)
    {
        if ($this->isEmpty()) {
            return null;
        }

        $keys = array_rand($this->items, $amount);

        if (is_array($keys)) {
            return new static(array_intersect_key($this->items, array_flip($keys)));
        }

        return $this->items[$keys];
    }

    /**
     * Flip items in the collection
     * @return Collection
     */
    public function flip($arr = false)
    {
        if ($arr != false) {
            return array_flip($arr);
        }
        return new static(array_flip($this->items));
    }

    /**
     * Map over each of the items in the collection
     * @param  Closure $iterator
     * @return Collection
     */
    public function map(Closure $iterator)
    {
        return new static(array_map($iterator, $this->items));
    }

    /**
     * Filter items within the collection
     * @param  Closure $filter
     * @return Collection
     */
    public function filter(Closure $filter)
    {
        return new static(array_filter($this->items, $filter));
    }

    /**
     * Reduce the collection to a single value
     * @param  Closure $callback
     * @param  mixed $initial
     * @return Collection
     */
    public function reduce(Closure $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Sum the items in the collection
     * @param  string $key
     */
    public function sum($key = null)
    {
        if (is_null($key)) {
            return array_sum($this->items);
        }

        if (is_string($key)) {
            return $this->map(function ($item) use ($key) {
                if (is_object($item)) {
                    return $item->$key;
                }
                if (is_array($item)) {
                    return $item[$key];
                }
            })->sum();
        }
    }

    /**
     * @param null $array
     * @return float
     */
    public function avg($array = null)
    {
        if (is_null($array) or !is_array($array)) {
            $array = $this->items;
        }
        $a = array_filter($array);
        $average = array_sum($a) / count($a);
        return $average;
    }

    /**
     * Concatenate items into a string
     * @param  string $glue
     */
    public function implode($glue = '')
    {
        return implode($glue, $this->items);
    }

    /**
     * Return total items in collection
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Check if the collection is empty
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Return the collection as an array
     * @return Array
     */
    public function toArray()
    {
        return $this->asArray($this->items);
    }

    /**
     * Return the collection as JSON
     * @param  integer $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->items, $options);
    }

    /**
     * Iterate over items in collection
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * JSON Serialize items in the collection
     */
    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * Забывает часть массива
     * @param array $arr
     * @return mixed
     */
    public function forget(array $arr, $array = false)
    {

        $keys = array_flip($arr);
        if (!$array) {
            $return = 1;
            $array = $this->toArray();
        }
        foreach ($array as $k => $v) {

            if (isset($keys[$k])) {
                unset($array[$k]);
            }
            if (is_array($v) or is_object($v)) {
                $array[$k] = $this->forget($arr, $v);
            }
        }
        if (isset($return)) {
            return new static ($array);
        } else {
            return $array;
        }
    }

    /**
     * @param $key
     * @return static
     */
    public function pluck($val, $key = false, $array = false)
    {

        if (!$array) {
            $return = 1;
            $array = $this->toArray();
        }
        foreach ($array as $k => $v) {
            if ($val != $k) {
                unset($array[$k]);
            }
            if (is_array($v) or is_object($v)) {
                $array[$k] = $this->pluck($val, $key, $v);
            }
        }
        if (isset($return)) {
            return new static ($array);
        } else {
            return $array;
        }
    }

    /**
     * Transform collection to XML format
     * Преобразует коллекцию в XML формат
     *
     * @return string
     */
    public function toXml()
    {

        return ArrayToXml::convert($this->toArray($this->items));
    }

}
