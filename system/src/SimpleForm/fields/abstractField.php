<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 12.11.2017
 * Time: 10:53
 */
namespace SimpleForm\fields;

/**
 * Абстрактный класс который содержит базое описания полей
 * Class abstractField
 * @package SimpleForm\fields
 */
abstract class abstractField
{

	/**
	 * Имя поля, обязательно для всех! Может включать указатель что это массив
	 * @var string
	 */
	private $name = "";
	/**
	 * Перечень или один класс, который применятся для поля
	 * @var array
	 */
	private $class = [];
	/**
	 * Стили, передается массив, либо один стиль как строка.. Класс сам все поймет и применит
	 * @var
	 */
	private $style = [];
	/**
	 * Уникальный индификатор поля, уникальность будет проверена в рамках этого класса!
	 * @var string
	 */
	private $id = "";
	/**
	 * Перечень кастомных полей
	 * @var array
	 */
	private $custom = [];
	/**
	 * События
	 * @var array
	 */
	private $events = [
		'onclick'  => [],
		'onchange' => [],
		'onfocus'  => [],
		'onblur'   => []
	];
	/**
	 * Значение поля
	 * @var mixed
	 */
	private $value = false;
	/**
	 * Значение по умолчанию
	 * @var mixed
	 */
	private $defValue = 0;

	public function __construct($name = false)
	{
		if ($name!=false) {
			$this->setName($name);
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return " name='".$this->name."' ";
	}

	/**
	 * @param string $name
	 */
	private function setName($name)
	{
		if (!is_string($name) or strlen($name) < 1 or empty($name)) {
			throw new \Exception('ИМЯ должно быть строкой и не пустой');
		}
		$this->name = $name;
		return $this;
	}

	/**
	 * Возвращает класс для поля в виде строки
	 * @return string
	 */
	public function getClass()
	{
		if (count($this->class) > 0) {
			return " class='".implode(" ", $this->class)."' ";
		} else {
			return "";
		}
	}

	/**
	 * @param array $class
	 */
	public function setClass($class)
	{
		if (is_numeric($class)) {
			throw new \Exception('Класс не может быть числом, всегда начинается с буквы!');
		}
		if (is_string($class)) {
			$this->class[] = $class;
		}
		if (is_array($class)) {
			foreach ($class as $k => $v) {
				$this->class[] = $v;
			}
		}
		return $this;
	}

	/**
	 * Возвращает стили
	 * @return mixed
	 */
	public function getStyle()
	{
		if (count($this->style) > 0) {
			return " style='".implode(" ", $this->style)."' ";
		} else {
			return "";
		}
	}

	/**
	 * Примает массив готовых строк, либо массив ключ значение
	 * Предполагается массив вида ['position:top', 'margin:0] либо ['margin'=>'0px','padding'=>'15px']
	 *
	 * @param mixed $style
	 */
	public function setStyle(array $style)
	{
		if (is_array($style)) {
			foreach ($style as $k => $v) {
				//расставляем разделитель в конце
				if (substr($v, 0 - 1)!=";") {
					$v = $v.";";
				}
				if (strpos($v, ":")) {
					$this->style[] = $v;
				} else {
					$this->style[] = $k.":".$v;
				}
			}
		} else {
			throw new \Exception("Предполагается массив вида ['position:top', 'margin:0] либо ['margin'=>'0px','padding'=>'15px']");
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return " id ='".$this->id."'";
	}

	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		if (!is_string($id)) {
			throw new \Exception('ID должно начинаться буквы и буть строкой!');
		}
		$this->id = $id;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getCustom()
	{
		return implode(" ", $this->custom);
	}

	/**
	 * Предполагается массив вида ['data-id=5', 'onclick=myfunction()'] либо ['onclick'=>'myfunction()','data-id'=>'5']
	 * Обратите внимание, что когда не передается ключ для маасива, он становится числовым, и это признак того, что вы
	 * передаете значение без ключа массив первого типа, если же ключ не числовой, значит массив второго типа
	 *
	 * @param array $custom
	 */
	public function setCustom(array $custom)
	{
		if (!is_array($custom)) {
			foreach ($custom as $k => $v) {
				if (is_numeric($k)) {
					$this->custom[] = $custom;
				} else {
					$this->custom[] = $k."= '".$v."'";
				}
			}
		} else {
			throw new \Exception("Предполагается массив вида ['data-id=5', 'onclick=myfunction()'] либо ['onclick'=>'myfunction()','data-id'=>'5']");
		}
		return $this;
	}

	/**
	 * Добавляет событие по клику, можно и через customs.. но так удобнее
	 *
	 * @param $event
	 */
	public function onClick($event)
	{
		if (!is_string($event)) {
			throw  new \Exception('Событие должно быть строкой!');
		}
		$this->events['onclick'] = $event;
		return $this;
	}

	/**
	 * Добавляет событие по фокусу, можно и через customs.. но так удобнее
	 *
	 * @param $event
	 */
	public function onFocus($event)
	{
		if (!is_string($event)) {
			throw  new \Exception('Событие должно быть строкой!');
		}
		$this->events['onfocus'] = $event;
		return $this;
	}

	/**
	 * Добавляет событие по изменению, можно и через customs.. но так удобнее
	 *
	 * @param $event
	 */
	public function onChange($event)
	{
		if (!is_string($event)) {
			throw  new \Exception('Событие должно быть строкой!');
		}
		$this->events['onchange'] = $event;
		return $this;
	}

	/**
	 * Добавляет событие по потере фокуса, можно и через customs.. но так удобнее
	 *
	 * @param $event
	 */
	public function onBlur($event)
	{
		if (!is_string($event)) {
			throw  new \Exception('Событие должно быть строкой!');
		}
		$this->events['onblur'] = $event;
		return $this;
	}

	/**
	 * Возвращает строку events
	 * @return string
	 */
	public function getEvents()
	{
		if (count($this->events) > 0) {
			$str = "";
			foreach ($this->events as $k => $v) {
				if (count($v) > 0) {
					$str .= $k."='".implode(" ", $v)."'";
				}
			}
			return $str;
		} else {
			return "";
		}
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		if (!empty($this->value)) {
			return $this->value;
		} else {
			return "";
		}
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	/**
	 * Получить дефолтовое значение для поля
	 * @return mixed $value
	 */
	public function getDefValue()
	{
		return $this->defValue;
	}

	/**
	 * @param mixed $defValue
	 */
	public function setDefValue($defValue)
	{
		$this->defValue = $defValue;
	}
}