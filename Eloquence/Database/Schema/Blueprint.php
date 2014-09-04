<?php

namespace Eloquence\Database\Schema;

/**
 * Class Blueprint
 *
 * Extends Laravel's Blueprint functionality by supporting various other types of field creations,
 * mainly just to ensure that
 *
 * @package Eloquence\Database\Schema
 */
class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
	/**
	 * Adds support for the generation of UUID fields. That is, char fields
	 * with a fixed length of 36 characters.
	 *
	 * @param $name
	 * @return \Illuminate\Support\Fluent
	 */
	public function uuid($name)
	{
		return $this->char($name, $length = 36);
	}
}
