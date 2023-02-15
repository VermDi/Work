<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 12.11.2017
 * Time: 11:47
 */
namespace SimpleForm\fields;

/**
 * Class Input
 * @package SimpleForm\fields
 */
class Input extends abstractField
{

	/**
	 * @var bool
	 */
	private $type = false;

	/**
	 * Input constructor.
	 *
	 * @param bool $name
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		return $this;
	}

	/**
	 * @param $type
	 *
	 * @throws \Exception
	 */
	public function setType($type)
	{
		if (!is_string($type)) {
			throw new \Exception('Тип должен быть строкой вида: text');
		}
		if (strpos($type, "type")) {
			$this->type = "";
		} else {
			$this->type = "type='".$type."'";
		}
	}

	/**
	 * @return bool|string
	 */
	public function getType()
	{
		if ($this->type==false) {
			return "type='text'";
		} else {
			return $this->type;
		}
	}

	/**
	 * Обратите внимание что возвращается именно строка и именно возвращается!
	 * @return string
	 */
	public function show()
	{
		return "<input ".$this->getType().$this->getName().$this->getClass().$this->getId().$this->getCustom().$this->getStyle().$this->getEvents()." value='".$this->getValue()."'>";
	}
}

?>