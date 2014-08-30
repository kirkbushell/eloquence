<?php

namespace Eloquence\Database\Traits;

trait CamelCaseModel
{
	/**
	 * Alter eloquent model behaviour so that model attributes can be accessed via camelCase, but more importantly,
	 * attributes also get returned as camelCase fields.
	 *
	 * @var bool
	 */
	public $enforceCamelCase = true;

	/**
	 * Overloads the eloquent setAttribute method to ensure that fields accessed
	 * in any case are converted to snake_case, which is the defacto standard
	 * for field names in databases.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function setAttribute($key, $value)
	{
		parent::setAttribute($this->getTrueKey($key), $value);
	}

	/**
	 * Retrieve a given attribute but allow it to be accessed via alternative case methods (such as camelCase).
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getAttribute($key)
	{
		return parent::getAttribute($this->getTrueKey($key));
	}

	/**
	 * Return the attributes for the model, converting field casing if necessary.
	 *
	 * @return array
	 */
	public function attributesToArray()
	{
		$attributes = parent::attributesToArray();
		$convertedAttributes = [];

		foreach ($attributes as $key => $value) {
			if ($this->enforceCamelCase) {
				$key = camel_case($key);
			}

			$convertedAttributes[$key] = $value;
		}

		return $convertedAttributes;
	}

	/**
	 * If the field names need to be converted so that they can be accessed by camelCase, then we can do that here.
	 *
	 * @param $key
	 * @return string
	 */
	protected function getTrueKey($key)
	{
		return snake_case($key);
	}
}
