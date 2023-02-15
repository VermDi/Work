<?php

namespace core;

abstract class Model
{
	public static $instance;
	public        $_pdo              = null;
	public        $table             = '';
	public        $fieldsType        = [];
	public        $sanitize          = [];
	private       $_select           = '*';
	private       $_from             = '';
	private       $_where            = null;
	private       $_limit            = null;
	private       $_join             = null;
	private       $_fillable         = null;
	private       $_noEscape         = null;
	private       $_orderBy          = null;
	private       $_groupBy          = null;
	private       $_having           = null;
	private       $_offset           = null;
	private       $_grouped          = false;
	private       $_numRows          = 0;
	private       $_insertId         = null;
	private       $_query            = null;
	private       $_error            = null;
	private       $_result           = null;
	private       $_prefix           = null;
	public        $_debug_mode       = false;
	protected     $_find_id          = false;
	public        $show_error        = 1;
	private       $_secondTable      = null;
	protected     $_transactionCount = 0;
	private       $_op               = [
		'=',
		'!=',
		'<',
		'>',
		'<=',
		'>=',
		'<>',
		'is',
	];
	private       $_queryCount       = 0;
	private       $_final_query      = null;
	public        $_pk               = 'id';
	private       $_cache            = null;
	const _class = __CLASS__;
	private $allow_word = [
		'NULL' => 1, 'NOW()' => 1, 'CURDATE()' => 1, 'SYSDATE()' => 1,
	];

	function __construct($pk = false)
	{
		if ($pk != false) {
			$this->_find_id = $pk;
		} //это для has many
		$this->table($this->table);
		$this->_result = new \stdClass(); //без этой херни не работало
	}

	private function pdo()
	{
		if ($this->_pdo == null) {
			$this->_pdo = Db::instance();
		}
		return $this->_pdo;
	}

	public static function instance()
	{
		return static::$instance ?: new static();
	}

	/**
	 * @param $name
	 * @param $value
	 */
	function __set($name, $value)
	{
		$this->setValue($name, $value);
	}

	public function setPk($key)
	{
		$this->_pk = $key;

	}

	/**
	 * Данный метод взводит спец обработку для полей, на момент вставки
	 *
	 * @param $key
	 * @param $val
	 *
	 * @return string
	 */
	private function specialSet($key, $val)
	{
		if (!isset($this->fieldsType[$key]) or empty($val)) {
			if (is_null($val) and !is_numeric($val)) {
				return 'null';
			}
			return $val;
		} else {
			if ($this->fieldsType[$key] == 'geoPoint') { //geoPoint insert point from text ->  x,y
				return 'POINT(' . $val . ')';
			}
		}
	}

	/**
	 * Данный метод получает данные из спец полей, с возвратом в первоначальное значение.
	 *
	 * @param $col
	 *
	 * @return string
	 */
	private function specialSelectColumn($col)
	{
		if (!isset($this->fieldsType[$col])) {
			return $col;
		} else {
			if ($this->fieldsType[$col] == 'geoPoint') {
				return 'REPLACE(REPLACE(SUBSTRING(st_astext(' . $col . '), 7),\')\',\'\'),\' \', \',\') AS ' . $col; //return string -> x,y
			}
		}
	}


	/**
	 * Установка переменной с предварительной проверкой
	 */
	function setValue($name, $value)
	{
		if (method_exists($this, "check" . $name)) {
			if (($time = $this->{"check" . $name}($value)) == false) { //$this->_result->$name =
				$this->_error[$name] = " Ошибка заполнения поля " . $name . " ";
			} else {
				$this->_result->$name = $value;
			}
		} else {
			if (empty($this->$name) and $this->$name === null and empty($value) and !is_numeric($value)) {
				$value = null;
			}
			if ($this->_result == null) {
				$this->_result == new \stdClass();
			}
			if (!empty($this->_result)) {
				$this->_result->$name = $value;
			}
		}
	}


	/**
	 * Перечень полей, которые не надо экранировать
	 */
	public function setNoEscape($data)
	{
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$this->_noEscape[] = $v;
			}
		} else {
			$this->_noEscape[] = $data;
		}
		return $this;

	}

	public function clearNoEscape()
	{
		$this->_noEscape = null;
	}

	/**
	 * Возвращает ответ на вопрос, есть ошибки Да / НЕТ
	 *
	 * @return bool
	 */
	public function noError()
	{
		if ($this->_error == null) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Удаляет из даты все
	 *
	 * @param $data
	 */
	public function clearData($data)
	{
		$cleared = false;
		foreach ($this->_result as $k => $v) {
			if ($k != $this->_pk) {
				unset($data[$k]);
				$cleared[$k] = $v;
			}
		}
		return $cleared;
	}

	/**
	 * Возвращает очищенную от данных этой модели данные
	 *
	 * @param $data
	 */
	public function takeData($data)
	{
		foreach ($this->_result as $k => $v) {
			if (isset($data[$k]) and $k != $this->_pk) {
				unset($data[$k]);
			}
		}
		return $data;
	}

	public function getAllData()
	{
		$result = [];
		foreach ($this->_result as $k => $v) {
			//if ($k != $this->_pk) {
			//unset($data[$k]);
			$result[$k] = $v;
			//}
		}
		return $result;
	}

	/**
	 * Метод заполняет созданный объект данными из любого массива
	 *
	 * @param $data
	 */
	function fill($data)
	{

		if (!is_object($data)) {
			$data = (object)$data;
		}
		if (!empty($data->{$this->_pk})) {
			$this->setValue($this->_pk, $data->{$this->_pk});
		}
		foreach ($this->_result as $k => $v) {
			if (isset($data->$k)) {
				$this->setValue($k, $data->$k);
			}
		}
		return $this;
	}


	/**
	 * Получает имена колонок таблицы, и возвращет их в виде массива
	 *
	 * @return array
	 */
	function getColumnsName()
	{
		$query = $this->pdo()->query('SHOW COLUMNS FROM ' . $this->table)->fetchAll();
		$result = [];
		if ($query) {
			foreach ($query as $item)
				$result[] = $item['Field'];
		}
		return $result;
	}

	public function beforeSave()
	{
		return $this;
	}

	public function afterSave()
	{
		return $this;
	}


	/**
	 * Простой метод, позволяющий сохранять данные на основе переданного массива или объекта
	 * При этом если в массиве или объекте ЕСТЬ, primary ключ, то идет UPDATE, в противном случае INSERT
	 * Есть и третий способ, если заполнен объет, то вызов этого метода, сделает описанные выше действия
	 * на созданном объекте
	 * Пример:
	 * $model->save(array('id'=>1, 'url' = '/ho-ho-ho')); ОБНОВИТ ЗАПИСЬ
	 * $model->save(array('url' = '/ho-ho-ho')); Вставит запипис
	 * --------------
	 * $model->ID=1;
	 * $model->URL='/ho-ho-ho';
	 * $model->save();
	 * Произойдет обновление записи 1.
	 *
	 * @param bool $data
	 *
	 * @return bool or primaryKey
	 */
	function save($data = false)
	{
		if ($this->_error != null) {
			$this->_print_error();
		} //если есть ошибка не даем сохранить
		if ($data != false) {
			if (is_array($data)) {
				$data = (object)$data;
			}
			$this->_result = $data;
		}
		if ($this->beforeSave() === false) {
			return false;
		}
		$pk = $this->_pk;
		if (isset($this->_result->$pk) and !empty(trim($this->_result->$pk)) and $this->_result->$pk != null) {
			return $this->update($this->_result);
		} else {
			return $this->insert($this->_result);
		}

	}

	function __get($name)
	{
		if (isset($this->_result->$name)) {
			return $this->_result->$name;
		}
		if (method_exists($this, $name)) {
			return $this->{$name}();
		}
		return null;
	}

	public function __isset($key)
	{
		if (isset($this->_result->$key)) {
			return (false === empty($this->_result->$key));
		} else {
			return null;
		}
	}

	/**
	 * @param $table
	 *
	 * @return $this
	 */
	public function table($table)
	{
		if (is_array($table)) {
			$f = '';
			foreach ($table as $key) {
				$f .= $this->_prefix . $key . ', ';
			}
			$this->_from = rtrim($f, ', ');
		} else {
			$this->_from = $this->_prefix . $table;
		}
		return $this;
	}

	/**
	 * Метод устанавливает поля для выборки. При этом поля могу переданы, как строкй - одно поле, так и массивом
	 * $model->select('id');
	 * $model->select(array('id','ur'));
	 * $model->select('*'); выбрать все
	 * Можно и строкой:
	 * $model->select(" COUNT(id)");
	 *
	 * @param $fields
	 *
	 * @return $this
	 */
	public function select($fields)
	{
		$select = (is_array($fields) ? implode(", ", $fields) : $fields);
		$this->_select = ($this->_select == '*' ? $select : $this->_select . ", " . $select);
		return $this;
	}

	/**
	 * Получить максимальное значение поля
	 *
	 * @param $field
	 * @param null $name
	 *
	 * @return $this
	 */
	public function max($field, $name = null)
	{
		$func = "MAX(" . $field . ")" . (!is_null($name) ? " AS " . $name : "");
		$this->_select = ($this->_select == '*' ? $func : $this->_select . ", " . $func);
		return $this;
	}

	/**
	 * Получить минимальное значение поля.
	 *
	 * @param $field
	 * @param null $name
	 *
	 * @return $this
	 */
	public function min($field, $name = null)
	{
		$func = "MIN(" . $field . ")" . (!is_null($name) ? " AS " . $name : "");
		$this->_select = ($this->_select == '*' ? $func : $this->_select . ", " . $func);
		return $this;
	}

	/**
	 * Получить сумму по колонке
	 *
	 * @param $field
	 * @param null $name
	 *
	 * @return $this
	 */
	public function sum($field, $name = null)
	{
		$func = "SUM(" . $field . ")" . (!is_null($name) ? " AS " . $name : "");
		$this->_select = ($this->_select == '*' ? $func : $this->_select . ", " . $func);
		return $this;
	}

	/**
	 * Посчитать количество элементов
	 *
	 * @param $field
	 * @param null $name
	 *
	 * @return $this
	 */
	public function count($field, $name = null)
	{
		$func = "COUNT(" . $field . ")" . (!is_null($name) ? " AS " . $name : "");
		$this->_select = ($this->_select == '*' ? $func : $this->_select . ", " . $func);
		return $this;
	}

	/**
	 * Вычислить среднее
	 *
	 * @param $field
	 * @param null $name
	 *
	 * @return $this
	 */
	public function avg($field, $name = null)
	{
		$func = "AVG(" . $field . ")" . (!is_null($name) ? " AS " . $name : "");
		$this->_select = ($this->_select == '*' ? $func : $this->_select . ", " . $func);
		return $this;
	}

	public function join($table, $field1 = null, $op = null, $field2 = null, $type = '')
	{
		$this->_secondTable = $table;
		$field1 = $this->_prefix . $field1;
		$field2 = $this->_prefix . $field2;
		if (strpos($field1, '.') === false) {
			$field1 = $table . "." . $field1;
		}
		if (strpos($field2, '.') === false) {
			$field2 = $this->table . "." . $field2;
		}
		$on = $field1;
		$table = $this->_prefix . $table;
		if (!is_null($op)) {
			$on = (!in_array($op, $this->_op) ? $field1 . ' = ' . $op : $field1 . ' ' . $op . ' ' . $field2);
		}
		if (is_null($this->_join)) {
			$this->_join = ' ' . $type . 'JOIN' . ' ' . $table . ' ON ' . $on;
		} else {
			$this->_join = $this->_join . ' ' . $type . 'JOIN' . ' ' . $table . ' ON ' . $on;
		}
		return $this;
	}

	/**
	 * Добавляет дополнительные параметры фильтрации в join запрос
	 *
	 * @param $field1
	 * @param $op
	 * @param $field2
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function andJoinCondition($field1, $op = null, $field2)
	{
		$field1 = $this->_prefix . $field1;
		$field2 = $this->_prefix . $field2;
		if (strpos($field2, '.') === false) {
			$field2 = $this->table . "." . $field2;
		}
		if (strpos($field1, '.') === false) {
			$field1 = $this->_secondTable . "." . $field1;
		}
		if (!is_null($op)) {
			$on = (!in_array($op, $this->_op) ? $field1 . ' = ' . $op : $field1 . ' ' . $op . ' ' . $field2);
		}
		if (is_null($this->_join)) {
			throw new \Exception('JOIN ПУСТОЙ');
		}
		$this->_join .= " and " . $on;
		return $this;
	}

	/**
	 * Добавляет дополнительные параметры фильтрации в join запрос
	 *
	 * @param $field1
	 * @param $op
	 * @param $field2
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function orJoinCondition($field1, $op, $field2)
	{
		$field1 = $this->_prefix . $field1;
		$field2 = $this->_prefix . $field2;
		if (strpos($field2, '.') === false) {
			$field2 = $this->table . "." . $field2;
		}
		if (strpos($field1, '.') === false) {
			$field1 = $this->_secondTable . "." . $field1;
		}

		if (!is_null($op)) {
			$on = (!in_array($op, $this->_op) ? $this->_prefix . $field1 . ' = ' . $this->_prefix . $op : $this->_prefix . $field1 . ' ' . $op . ' ' . $this->_prefix . $field2);
		}
		if (is_null($this->_join)) {
			throw new \Exception;
		}
		$this->_join .= " or " . $on;
		return $this;
	}


	public function innerJoin($table, $field1, $op = '', $field2 = '')
	{
		$this->join($table, $field1, $op, $field2, 'INNER ');
		return $this;
	}

	public function leftJoin($table, $field1, $op = '', $field2 = '')
	{
		$this->join($table, $field1, $op, $field2, 'LEFT ');
		return $this;
	}

	public function rightJoin($table, $field1, $op = '', $field2 = '')
	{
		$this->join($table, $field1, $op, $field2, 'RIGHT ');
		return $this;
	}

	public function fullOuterJoin($table, $field1, $op = '', $field2 = '')
	{
		$this->join($table, $field1, $op, $field2, 'FULL OUTER ');
		return $this;
	}

	public function leftOuterJoin($table, $field1, $op = '', $field2 = '')
	{
		$this->join($table, $field1, $op, $field2, 'LEFT OUTER ');
		return $this;
	}

	public function rightOuterJoin($table, $field1, $op = '', $field2 = '')
	{
		$this->join($table, $field1, $op, $field2, 'RIGHT OUTER ');
		return $this;
	}

	/**
	 * Пример использования
	 * $model->whereRaw(' ( ( id<>5 AND date=NOW() + INTERVAL 1 DAY ) OR id=1) AND id<>7 ');
	 *
	 * @param        $where  - строка 'id<>5'
	 * @param string $and_or - AND или OR юзать
	 *
	 * @return $this
	 */
	public function whereRaw($where, $and_or = 'AND')
	{
		if (!is_string($where)) {
			die('whereRaw is not string');
		}
		if (is_null($this->_where)) {
			$this->_where = $where;
		} else {
			$this->_where = $this->_where . ' ' . $and_or . ' ' . $where;
		}
		return $this;
	}

	/**
	 * Пример использования, вывод примера будет [where id >5]
	 * $model->where('id','>=','5');
	 *
	 *
	 * Также можно делать груповое where, два условия ниже выведутся как [where (a=4 or a=5)]
	 * Пример:
	 * $model->where(function($q){
	 *      $q->where('a','=',3)->orWhere('a','=',5);
	 * })
	 *
	 * @param        $where  - поле
	 * @param null $op       - условие > < ....
	 * @param null $val      - значение
	 * @param string $type
	 * @param string $and_or - если where передано условие в виде массива, то здесь можно указать AND или OR юзать
	 *
	 * @return $this
	 */
	public function where($where, $op = null, $val = null, $type = '', $and_or = 'AND')
	{
		if ($val instanceof self) {
			$this->_noEscape[] = $where;
			$val = "(" . $val->getQuery() . ")";
		}
		if (is_array($where)) {
			$_where = [];
			foreach ($where as $column => $data) {
				if (is_array($this->_noEscape) and in_array($column, $this->_noEscape)) {
					$_where[] = $type . $column . '=' . $data;
				} else {
					$_where[] = $type . $column . '=' . $this->escape($data);
				}
			}
			$where = implode(' ' . $and_or . ' ', $_where);
		} elseif ($where instanceof \Closure) {
			return $this->grouped($where);
		} else {
			if (is_array($op)) {
				$x = explode('?', $where);
				$w = '';
				foreach ($x as $k => $v) {
					if (!empty($v)) {
						$w .= $type . $v . (isset($op[$k]) ? $this->escape($op[$k]) : '');
					}
				}
				$where = $w;
			} elseif (!in_array($op, $this->_op) || $op == false) {
				$where = $type . $where . ' = ' . $this->escape($op);
			} else {
				if (is_array($this->_noEscape) and in_array($where, $this->_noEscape)) {
					$where = $type . $where . ' ' . $op . ' ' . $val;
				} else {
					$where = $type . $where . ' ' . $op . ' ' . $this->escape($val);
				}
			}
		}
		if ($this->_grouped) {
			$where = '(' . $where;
			$this->_grouped = false;
		}
		if (is_null($this->_where)) {
			$this->_where = $where;
		} else {
			$this->_where = $this->_where . ' ' . $and_or . ' ' . $where;
		}
		return $this;
	}

	public function orWhere($where, $op = null, $val = null)
	{
		$this->where($where, $op, $val, '', 'OR');
		return $this;
	}

	public function notWhere($where, $op = null, $val = null)
	{
		$this->where($where, $op, $val, 'NOT ', 'AND');
		return $this;
	}

	public function orNotWhere($where, $op = null, $val = null)
	{
		$this->where($where, $op, $val, 'NOT ', 'OR');
		return $this;
	}

	/**
	 * Гда конкретная дата
	 *
	 * @param $field
	 * @param $date
	 *
	 * @return $this
	 */
	public function whereDate($field, $date)
	{
		$d = strtotime($date);
		$this->where($field, '=', date("Y-m-d H:i:s", $d));
		return $this;
	}

	/**
	 * Условие текущего времени
	 *
	 * @param $field
	 *
	 * @return $this
	 */
	public function whereDateNow($field)
	{
		$this->where($field, '=', date("Y-m-d H:i:s", time()));
		return $this;
	}


	/**
	 * Блок инкремента, увеличивает поле на заданное число или 1
	 *
	 * @param $field
	 * @param int $num
	 *
	 * @return bool
	 */
	public function increment($field, $num = 1)
	{
		$this->$field = $this->$field + $num;
		return $this->save();
	}

	/**
	 * * Блок декремента, уменьшает поле на заданное число или 1
	 *
	 * @param $field
	 * @param int $num
	 *
	 * @return bool
	 */
	public function decrement($field, $num = 1)
	{
		$this->$field = $this->$field - $num;
		return $this->save();
	}


	public function grouped(\Closure $obj)
	{
		$this->_grouped = true;
		call_user_func_array($obj, [$this]);
		$this->_where .= ')';
		return $this;
	}

	/**
	 * Метод принимае либо массив ключей, либо экземпляр модели.
	 *
	 * @param string $field  поле по котомоу будет совпадение
	 * @param null $keys     - Либо массив ключей, либо объект модели
	 * @param string $type   - NOT, OR  - приставка к IN
	 * @param string $and_or - AND or OR - применятся к условию
	 *
	 * @return $this
	 */
	public function in($field, $keys = null, $type = '', $and_or = 'AND')
	{
		/*
		 * Если передан массив
		 */
		if (is_array($keys)) {
			$_keys = [];
			foreach ($keys as $k => $v) {
				$_keys[] = $this->escape($v);
			}
			$keys = implode(', ', $_keys);
		}
		/*
		 * Если передан объект этого класса
		 */
		if (is_object($keys) and method_exists($keys, "getQuery")) {
			$keys = $keys->getQuery();
		}
		if (!is_null($keys)) {
			if (is_null($this->_where)) {
				$this->_where = $field . ' ' . $type . 'IN (' . $keys . ')';
			} else {
				$this->_where = $this->_where . ' ' . $and_or . ' ' . $field . ' ' . $type . 'IN (' . $keys . ')';
			}
		}
		return $this;
	}

	public function notIn($field, $keys = null)
	{
		$this->in($field, $keys, 'NOT ', 'AND');
		return $this;
	}

	public function orIn($field, $keys = null)
	{
		$this->in($field, $keys, '', 'OR');
		return $this;
	}

	public function orNotIn($field, $keys = null)
	{
		$this->in($field, $keys, 'NOT ', 'OR');
		return $this;
	}

	public function between($field, $value1, $value2, $type = '', $and_or = 'AND')
	{

		if (is_null($this->_where)) {
			$this->_where = $field . ' ' . $type . 'BETWEEN ' . $this->escape($value1) . ' AND ' . $this->escape($value2);
			// echo $this->_where;
		} else {
			$this->_where = $this->_where . ' ' . $and_or . ' ' . $field . ' ' . $type . 'BETWEEN ' . $this->escape($value1) . ' AND ' . $this->escape($value2);
		}
		return $this;
	}

	public function notBetween($field, $value1, $value2)
	{
		$this->between($field, $value1, $value2, 'NOT ', 'AND');
		return $this;
	}

	public function orBetween($field, $value1, $value2)
	{
		$this->between($field, $value1, $value2, '', 'OR');
		return $this;
	}

	public function orNotBetween($field, $value1, $value2)
	{
		$this->between($field, $value1, $value2, 'NOT ', 'OR');
		return $this;
	}

	public function like($field, $data, $type = '', $and_or = 'AND')
	{
		$like = $this->escape($data);
		if (is_null($this->_where)) {
			$this->_where = $field . ' ' . $type . 'LIKE ' . $like;
		} else {
			$this->_where = $this->_where . ' ' . $and_or . ' ' . $field . ' ' . $type . 'LIKE ' . $like;
		}
		return $this;
	}

	public function orLike($field, $data)
	{
		$this->like($field, $data, '', 'OR');
		return $this;
	}

	public function notLike($field, $data)
	{
		$this->like($field, $data, 'NOT ', 'AND');
		return $this;
	}

	public function orNotLike($field, $data)
	{
		$this->like($field, $data, 'NOT ', 'OR');
		return $this;
	}

	public function limit($limit, $limitEnd = null)
	{
		if (!is_null($limitEnd)) {
			$this->_limit = $limit . ', ' . $limitEnd;
		} else {
			$this->_limit = $limit;
		}
		return $this;
	}


	/**
	 * Добавляет сдвиг выборки.
	 *
	 * @param $num
	 *
	 * @return $this
	 */
	public function offset($num)
	{
		if (is_numeric($num)) {
			$this->_offset = intval($num);
		}
		return $this;
	}

	public function orderBy($orderBy, $order_dir = null, $UPorDown = false)
	{
		if ($orderBy == null) {
			return $this;
		}
		if (!is_null($order_dir)) {
			$this->_orderBy = $orderBy . ' ' . strtoupper($order_dir);
		} else {
			if (stristr($orderBy, ' ') || $orderBy == 'rand()') {
				$this->_orderBy = $orderBy;
			} else {
				$this->_orderBy = $orderBy;
				$this->_orderBy .= ($UPorDown) ? " " . $UPorDown : ' ASC';
			}
		}
		return $this;
	}

	public function groupBy($groupBy)
	{
		if (is_array($groupBy)) {
			$this->_groupBy = implode(', ', $groupBy);
		} else {
			$this->_groupBy = $groupBy;
		}
		return $this;
	}

	public function having($field, $op = null, $val = null)
	{
		if (is_array($op)) {
			$x = explode('?', $field);
			$w = '';
			foreach ($x as $k => $v) {
				if (!empty($v)) {
					$w .= $v . (isset($op[$k]) ? $this->escape($op[$k]) : '');
				}
			}
			$this->_having = $w;
		} elseif (!in_array($op, $this->_op)) {
			$this->_having = $field . ' > ' . $this->escape($op);
		} else {
			$this->_having = $field . ' ' . $op . ' ' . $this->escape($val);
		}
		return $this;
	}

	public function numRows()
	{
		return $this->_numRows;
	}

	public function insertId()
	{
		return $this->_insertId;
	}

	public function error()
	{
		$msg = '<h1>Database Error</h1>';
		$msg .= '<h4>Query: <em style="font-weight:normal;">"' . $this->_query . '"</em></h4>';
		$msg .= '<h4>Error: <em style="font-weight:normal;">' . $this->_error . '</em></h4>';
		return $msg;
	}

	/**
	 * Получает объект записи. Либо false
	 *
	 * @param bool $pk
	 * @param bool $type
	 *
	 * @return bool|mixed|\PDOStatement|\stdClass|string
	 */
	public function getOne($pk = false, $type = false)
	{
		if (!is_array($pk) and !empty($pk)) {
			$this->where($this->_pk, "=", $pk);
		} elseif (!empty($pk)) {
			$this->where($pk);
		}
		return $this->get($type);
	}

	/**
	 * Чистит таблицу
	 *
	 * @return bool|mixed|\PDOStatement|\stdClass|string
	 */
	public function truncate()
	{
		return $this->query("TRUNCATE TABLE " . $this->table);
	}

	public function get($type = false)
	{
		$this->_limit = 1;
		$query = $this->prepareQuery();
		if ($type == true) {
			return $query;
		} else {
			return $this->query($query, false, (($type == 'array') ? true : false));
		}
	}

	protected function prepareQuery()
	{
		$this->beforeSelect(); //перед селектом
		if (count($this->fieldsType) > 0 and $this->_select == "*") {
			$cols = $this->getColumnsName();
			$arr = [];
			foreach ($cols as $item) {
				$arr[] = $this->specialSelectColumn($item);
			}
			$this->_select = implode(",", $arr);
		}
		$query = 'SELECT ' . $this->_select . ' FROM ' . $this->_from;
		if (!is_null($this->_join)) {
			$query .= $this->_join;
		}
		if (!is_null($this->_where)) {
			$query .= ' WHERE ' . $this->_where;
		}
		if (!is_null($this->_groupBy)) {
			$query .= ' GROUP BY ' . $this->_groupBy;
		}
		if (!is_null($this->_having)) {
			$query .= ' HAVING ' . $this->_having;
		}
		if (!is_null($this->_orderBy)) {
			$query .= ' ORDER BY ' . $this->_orderBy;
		}
		if (!is_null($this->_limit)) {
			$query .= ' LIMIT ' . $this->_limit;
		}
		if (!is_null($this->_offset)) {
			$query .= ' OFFSET  ' . $this->_offset;
		}
		return $this->_final_query = $query;
	}

	/**
	 * Метод по умолчанию возвращает массив объектов.
	 * Если выборка пустая, вернет пустой массив!!!
	 * Поэтому проверять на наличие элементов массива!     *
	 *
	 * @param bool $type
	 *
	 * @return array|object
	 */
	public function getAll($type = false)
	{
		if ($type === true) {
			$result = $this->prepareQuery();
		} else {
			$result = $this->query($this->prepareQuery(), true, (($type == 'array') ? true : false));
		}
		if (!$result) {
			return [];
		}
		return $result;
	}

	public function rm($keys)
	{
		if (!is_array($keys)) {
			unset($this->_result->$keys);
		}
		return $this;
	}

	public function insert($data = false)
	{
		if ($this->beforeSave() === false) {
			return false;
		}
		if ($this->beforeInsert() === false) {
			return false;
		}
		if ($data == false and !empty($this->_result)) {
			$data = (array)$this->_result;
		}
		if (empty($data) and empty($this->_result)) {
			throw new \Exception("ОШИБКА ЗАПРОСА INSERT НЕТ ДАННЫХ");
		}
		if (!empty($data)) {
			$this->reset();
		} //если передали массив сбрасываем все.

		if (is_object($data)) {
			$data = (array)$data;
		}
		$data = $this->sanitize($data);
		$end_val = [];
		foreach ($data as $k => $v) {
			if ($k == $this->_pk and empty($v)) {
				unset($data[$k]);
				continue;
			}
			$v = $this->specialSet($k, $v);

			/**
			 * TODO: добавить проверку на тип. чтобы при числе 0 работал
			 */
			if ((is_array($this->_noEscape) and in_array($k, $this->_noEscape)) or isset($this->fieldsType[$k])) {
				$end_val[] = $v;
			} else {
				if ($v === null) {
					$v = 'NULL';
				}
				$end_val[] = $this->escape($v);
			}
		}
		$columns = array_keys($data);
		$column = '`' . implode('`,`', $columns) . "`";
		$val = implode(', ', $end_val);
		$query = 'INSERT INTO ' . $this->_from . ' (' . $column . ') VALUES (' . $val . ')';
		if ($this->_debug_mode == false) {
			$query = $this->query($query);
			if ($query) {
				$this->_insertId = $this->pdo()->lastInsertId();
				$this->afterSave();
				return $this->insertId();
			} else {
				return false;
			}
		} else {
			echo $query . PHP_EOL;
		}
		return true;
	}

	/**
	 * Добавляет масс данные для вставки, при жтом не делается проверка на данные!! ВАЖНО на 09 07 2020
	 *
	 * @example $this->massInsert(['kods'], ['111','2222,'3333', ....'xxx']);
	 *
	 * @param array $keys        - ключи который вставляем
	 * @param array $values      - масиив значений
	 * @param int $limit         - сколько за раз ставить
	 * @param bool $error_ignore - игнорировать ошибки ?
	 *
	 * @return array возвращает массив результата выполенения пачек
	 * @throws \Exception
	 */
	public function massInsert(array $keys, array $values, $limit = 100, $error_ignore = true)
	{
		if (empty($data) or empty($values)) {
			throw new \Exception("ОШИБКА ЗАПРОСА MASS INSERT НЕТ ДАННЫХ");
		}
		$val = "";
		$i = 0;
		$result = [];
		foreach ($values as $k => $v) {
			$i++;
			$val .= "(" . implode(",", $v) . "),";
			if ($i > $limit) {
				$i = 0;
				$val = substr($val, 0, -1);
				$query = 'INSERT INTO ' . $this->_from . ' (' . implode(",", $keys) . ') VALUES (' . $val . ')';
				$result[$i] = $this->query($query);
			}

		}

		$query = 'INSERT INTO ' . $this->_from . ' (' . implode(",", $keys) . ') VALUES (' . $val . ')';
		$result[$i] = $this->query($query);

		return $result;
	}

	public function update($data = false)
	{
		if ($this->beforeUpdate() === false) {
			return false;
		}
		if (empty($data) and empty($this->_result)) {
			throw new \Exception("ОШИБКА ЗАПРОСА UPDATE НЕТ ДАННЫХ");
		}
		if (empty($data) and !empty($this->_result)) {
			$data = (array)$this->_result;
		}

		if (!empty($data)) {
			$this->reset();
		} //если передали массив сбрасываем все.
		if (empty($this->_from)) {
			$this->_from = $this->table;
		}
		$query = 'UPDATE ' . $this->_from . ' SET ';
		$values = [];

		if (is_array($data) or is_object($data)) {
			$data = $this->sanitize($data);
			foreach ($data as $column => $val) {

				$val = $this->specialSet($column, $val);
				if ($column == $this->_pk) {
					$this->where($this->_pk, "=", $val);
				}
				if ($column != $this->_pk) {

					if (empty($val) and ($val != 0 and is_numeric($val))) {
						if ($val === null) {
							$val = 'NULL';
						} else {
							$val = '';
						}
					}
					if ((is_array($this->_noEscape) and in_array($column, $this->_noEscape)) or isset($this->fieldsType[$column])) {
						$values[] = "`" . $column . '`=' . $val;
					} else {
						$values[] = "`" . $column . '`=' . $this->escape($val);
					}
				}
			}
			$query .= implode(',', $values);
		} else {
			$query .= $data;
		}

		if (!is_null($this->_where)) {
			$query .= ' WHERE ' . $this->_where;
		}
		if (!is_null($this->_orderBy)) {
			$query .= ' ORDER BY ' . $this->_orderBy;
		}
		if (!is_null($this->_limit)) {
			$query .= ' LIMIT ' . $this->_limit;
		}
		if (!is_null($this->_offset)) {
			$query .= ' OFFSET ' . $this->_offset;
		}
		$pk = $this->{$this->_pk};
		if ($this->_debug_mode == false) {
			$query = $this->query($query);
			if ($query) {
				$this->afterSave();
				return $pk;
			} else {
				return $query;
			}
		} else {
			echo $query;
		}
		return true;
	}

	public function delete($id = false)
	{

		$this->beforeDelete();
		if (is_array($id)) {
			$this->where($id);
		} elseif ($id != false) {
			$this->where($this->_pk, "=", $id);
		} else {
			$ptime = $this->_pk;
			if (intval($this->$ptime) >= 1) {
				$this->where($this->_pk, "=", $this->$ptime);
			}
		}
		$query = 'DELETE FROM ' . $this->_from;
		if (!is_null($this->_where)) {
			$query .= ' WHERE ' . $this->_where;
		}
		if (!is_null($this->_orderBy)) {
			$query .= ' ORDER BY ' . $this->_orderBy;
		}
		if (!is_null($this->_limit)) {
			$query .= ' LIMIT ' . $this->_limit;
		}
		if ($this->_debug_mode == false) {
			return $this->query($query);
		} else {
			echo $query;
		}
		return true;
	}

	public function query($query, $all = true, $array = false)
	{
		$this->reset();
		if (is_array($all)) {
			$x = explode('?', $query);
			$q = '';
			foreach ($x as $k => $v) {
				if (!empty($v)) {
					$q .= $v . (isset($all[$k]) ? $this->escape($all[$k]) : '');
				}
			}
			$query = $q;
		}
		$this->_query = preg_replace('/\s\s+|\t\t+/', ' ', trim($query));
		$type = false;

		if (substr(trim($this->_query), 0, 3) == "SEL") {
			$type = "S";
		}
		if (substr(trim($this->_query), 0, 3) == "UPD") {
			$type = "U";
		}
		if (substr(trim($this->_query), 0, 3) == "INS") {
			$type = "I";
		}
		if (substr(trim($this->_query), 0, 3) == "DEL") {
			$type = "D";
		}
		if ($this->_debug_mode == true) {
			echo $query;
			return false;
		}
		$cache = false;
		if (!is_null($this->_cache)) {
			$cache = $this->_cache->getCache($this->_query, $array);
		}
		if (!$cache && $type == "S") {
			try {
				$sql = $this->pdo()->query($this->_query);
			} catch (\PDOException $e) {

				$error = new Errors();
				$error->e500(false, $e);


			}

			if ($sql) {
				$this->_numRows = $sql->rowCount();
				if (($this->_numRows > 0)) {
					if ($all) {
						$q = [];
						while ($result = ($array == false) ? $sql->fetchAll(\PDO::FETCH_OBJ) : $sql->fetchAll(\PDO::FETCH_ASSOC)) {
							$q[] = $result;
						}
						$this->_result = $q[0];
					} else {
						$q = ($array == false) ? $sql->fetch(\PDO::FETCH_OBJ) : $sql->fetch(\PDO::FETCH_ASSOC);
						$this->_result = $q;
					}
				} else {
					//если затронуло менее 1 строки, знать вернем false
					return false;
				}
				if (!is_null($this->_cache)) {
					$this->_cache->setCache($this->_query, $this->_result);
				}
				$this->_cache = null;
			} else {
				$this->_cache = null;
				$this->_error = $this->pdo()->errorInfo();
				$this->_error = $this->_error[2];
				return $this->_query . $this->error();
			}
		} elseif ($type != "S") {
			// echo"here2";
			$this->_cache = null;
			try {
				$this->_result = $this->pdo()->query($this->_query);
			} catch (\PDOException $e) {
				if ($this->show_error == 1) {
					$this->_error = 'Выброшено исключение: ' . $e->getMessage() . "\n";
				}
				return false;
			}
			//echo"here3";
			if (!$this->_result) {
				$this->_error = $this->pdo()->errorInfo();
				$this->_error = $this->_error[2];
				return $this->_query . $this->error();
			} else {

			}
			//echo"<pre>";print_r($this);echo"</pre>";
		} else {
			$this->_cache = null;
			$this->_result = $cache;
		}
		$this->_queryCount++;
		if ($type == "U") {
			$this->afterUpdate();
		}
		if ($type == "S") {
			$this->afterSelect();
		}
		if ($type == "I") {
			$this->_insertId = $this->pdo()->lastInsertId();
			$this->afterInsert();
		}
		if ($type == "D") {
			$this->afterDelete();
		}
		return $this->_result;
	}

	public function analyze()
	{
		return $this->query('ANALYZE TABLE ' . $this->from, false);
	}

	public function check()
	{
		return $this->query('CHECK TABLE ' . $this->from, false);
	}

	public function checksum()
	{
		return $this->query('CHECKSUM TABLE ' . $this->from, false);
	}

	public function optimize()
	{
		return $this->query('OPTIMIZE TABLE ' . $this->from, false);
	}

	public function repair()
	{
		return $this->query('REPAIR TABLE ' . $this->from, false);
	}


	public function escape($data)
	{

		if (is_null($data) or $data == 'null' and !is_numeric($data)) {
			return 'null';
		}
		if (is_numeric($data)) {
			return '\'' . $data . '\'';
		}
		/*
		 * ВАЖНАЯ КОРЕКТИРОВКА! КАК ОКАЗАЛОСЬ В ЯЗЫКЕ 0 == 'null' Билять..
		 */
		if (is_null($data) or $data === 'null') {
			return 'null';
		}

		if (!is_array($data) && !is_object($data)) {
			if (isset($this->allow_word[$data])) {
				return $data;
			}
		}
		// echo"<pre>";print_r($data);echo"</pre>";
		if (!is_array($data)) {
			return $this->pdo()->quote(trim($data));
		}
	}

	public function cache($time)
	{
		$this->_cache = new Cache($this->_cacheDir, $time);
		return $this;
	}

	public function queryCount()
	{
		return $this->_queryCount;
	}

	public function getQuery()
	{
		$this->_query = $this->prepareQuery();
		return $this->_query;
	}

	public function transaction()
	{
		if (!$this->_transactionCount++) {
			return $this->pdo()->beginTransaction();
		}
		$this->pdo()->exec('SAVEPOINT trans' . $this->_transactionCount);
		return $this->_transactionCount >= 0;
	}

	public function commit()
	{
		if (!--$this->_transactionCount) {
			return $this->pdo()->commit();
		}
		return $this->_transactionCount >= 0;
	}

	public function rollBack()
	{
		if (--$this->_transactionCount) {
			$this->pdo()->exec('ROLLBACK TO trans' . ($this->_transactionCount + 1));
			return true;
		}
		return $this->pdo()->rollBack();
	}

	public function exec()
	{
		if (is_null($this->_query)) {
			return null;
		}
		$query = $this->pdo()->exec($this->_query);
		if ($query === false) {
			$this->_error = $this->pdo()->errorInfo()[2];
			return $this->_error();
		}
		return $query;
	}

	private function reset()
	{
		// $this->_select = '*';
		// $this->_from = null; //в модели это нуна!
		// $this->_where = null;
		$this->_limit = null;
		$this->_offset = null;
		$this->_orderBy = null;
		$this->_groupBy = null;
		$this->_having = null;
		//  $this->_join = null;
		$this->_grouped = false;
		$this->_numRows = 0;
		$this->_insertId = null;
		$this->_query = null;
		$this->_error = null;
		$this->_transactionCount = 0;
		//$this->_result   = [];
		return;
	}

	public function clear()
	{
		$this->_select = '*';
		$this->_from = null; //в модели это нуна!
		$this->_where = null;
		$this->_limit = null;
		$this->_offset = null;
		$this->_orderBy = null;
		$this->_groupBy = null;
		$this->_having = null;
		$this->_join = null;
		$this->_grouped = false;
		$this->_numRows = 0;
		$this->_insertId = null;
		$this->_query = null;
		$this->_error = null;
		$this->_result = new \stdClass();
		$this->_transactionCount = 0;
		$this->table($this->table);
		return $this;
	}

	public function from($param)
	{
		$this->table($param);
		return $this;
	}

	public function beforeSelect()
	{
		return true;
	}

	public function afterSelect()
	{
		return true;
	}

	public function beforeInsert()
	{
		return true;
	}

	public function afterInsert()
	{
		return true;
	}

	public function beforeUpdate()
	{
		return true;
	}

	public function afterUpdate()
	{
		return true;
	}

	public function beforeDelete()
	{
		return true;
	}

	public function afterDelete()
	{
		return true;
	}

	function __destruct()
	{
		$this->_pdo = null;
	}

	function _print_error()
	{
		echo "<pre>";
		print_r($this->getError());
		die();
	}

	function setError($error)
	{
		$this->_error = $error;
	}

	function hasError()
	{
		if ($this->_error) {
			return true;
		} else {
			return false;
		}
	}

	function getError()
	{
		return $this->_error;
	}

	/**
	 * @param Model $obj Объект того что будем получать
	 * @param bool $key  - поле для сцепки
	 *
	 * @return bool|mixed|\PDOStatement|\stdClass|string
	 */
	public function hasMany(self $obj, $key = false)
	{
		if ($key == false) {
			die('NOT WORK AT NOW');
		}
		return $obj->where($key, '=', $this->{$this->_pk})->getAll();
	}

	/**
	 * @param Model $obj Объект того что будем получать
	 * @param bool $key  - поле для сцепки
	 *
	 * @return bool|mixed|\PDOStatement|\stdClass|string
	 */
	public function hasOne(self $obj, $key = false)
	{
		if ($key == false) {
			die('NOT WORK AT NOW');
		}
		return $obj->where($key, '=', $this->{$this->_pk})->getOne();
	}

	/**
	 * @param Model $obj Объект того что будем получать
	 * @param bool $key  - поле для сцепки
	 *
	 * @return bool|mixed|\PDOStatement|\stdClass|string
	 */
	public function belongsTo(self $obj, $key = false)
	{
		if ($key == false) {
			die('NOT WORK AT NOW');
		}
		return $obj->where($this->_pk, '=', $key)->getOne();
	}

	/**
	 * @param $obj         - инстанс модели
	 * @param $obj_field   - поле для склейки от переданной модели
	 * @param false $field - необязательный параметр, поле текущей модели. Если нет используется PK
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function with($obj, $obj_field, $field = false)
	{
		if (!$obj instanceof Model) {
			throw new \Exception('Ошибка - нужна модель!');
		}
		if (!strpos($obj_field, '.')) {
			$obj_field = $obj->table . "." . $obj_field;
		}
		if ($field == false) {
			$this->leftJoin($obj->table, $this->_pk, '=', $obj_field);
		} else {
			if (!strpos($field, '.')) {
				$field = $this->table . "." . $field;
			}
			$this->leftJoin($obj->table, $field, '=', $obj_field);
		}
		return $this;
	}

	public function toArray()
	{
		return (array)$this->_result;
	}

	public function toCollection(Collection $collection)
	{
		return $collection->collect($this->asArray());
	}

	/**
	 * Метод проводит санирование всех указанных переменных в модели
	 */
	private function sanitize($data)
	{
		if (is_array($data)) {
			foreach ($this->sanitize as $val) {
				if (isset($data[$val])) {
					$data[$val] = htmlspecialchars($data[$val]);
				}
			}
			return $data;
		}
		if (is_object($data)) {
			foreach ($this->sanitize as $val) {
				if (isset($data->{$val})) {
					$data->{$val} = htmlspecialchars($data->{$val});
				}
			}
			return $data;
		}
		if (is_string($data)) {
			return htmlspecialchars($data);
		}
		return $data;
	}
}