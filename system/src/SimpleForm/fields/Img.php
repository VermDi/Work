<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 12.11.2017
 * Time: 12:07
 */
namespace SimpleForm\fields;

class Img extends Input
{

	public function __construct($name)
	{
		parent::__construct($name);
		$this->setType('file');
		return $this;
	}
}