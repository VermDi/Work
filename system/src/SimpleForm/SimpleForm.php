<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 12.11.2017
 * Time: 10:50
 */
namespace SimpleForm;

use SimpleForm\fields\Img;
use SimpleForm\fields\Input;

class SimpleForm
{

	protected static $instance;

	public static function instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @param $name
	 *
	 * @return Input
	 */
	public static function Input($name)
	{
		return new Input($name);
	}

	public static function Img($name)
	{
		return new Img($name);
	}
}