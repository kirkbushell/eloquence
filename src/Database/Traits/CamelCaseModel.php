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
     * @param mixed  $value
     */
    public function setAttribute($key, $value)
    {
        parent::setAttribute($this->getSnakeKey($key), $value);
    }

    /**
     * Retrieve a given attribute but allow it to be accessed via alternative case methods (such as camelCase).
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (method_exists($this, $key)) {
            return $this->getRelationshipFromMethod($key);
        }

        return parent::getAttribute($this->getSnakeKey($key));
    }

    /**
     * Return the attributes for the model, converting field casing if necessary.
     *
     * @return array
     */
    public function attributesToArray()
    {
        return $this->toCamelCase(parent::attributesToArray());
    }

    /**
     * Converts the attributes to a camel-case version, if applicable.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributesToArray();
    }

    /**
     * Get the model's relationships, converting field casing if necessary.
     *
     * @return array
     */
    public function relationsToArray()
    {
        return $this->toCamelCase(parent::relationsToArray());
    }

    /**
     * Converts a given array of attribute keys to the casing required by CamelCaseModel.
     *
     * @param mixed $attributes
     * @return array
     */
    public function toCamelCase($attributes)
    {
        $convertedAttributes = [];

        foreach ($attributes as $key => $value) {
            $key = $this->getTrueKey($key);
            $convertedAttributes[$key] = $value;
        }

        return $convertedAttributes;
    }

    /**
     * Get the model's original attribute values.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return array
     */
    public function getOriginal($key = null, $default = null)
    {
        return array_get($this->toCamelCase($this->original), $key, $default);
    }

    /**
     * Converts a given array of attribute keys to the casing required by CamelCaseModel.
     *
     * @param $attributes
     * @return array
     */
    private function toSnakeCase($attributes)
    {
        $convertedAttributes = [];

        foreach ($attributes as $key => $value) {
            $convertedAttributes[$this->getSnakeKey($key)] = $value;
        }

        return $convertedAttributes;
    }

    /**
     * Retrieves the true key name for a key.
     *
     * @param $key
     * @return string
     */
    public function getTrueKey($key)
    {
        // If the key is a pivot key, leave it alone - this is required internal behaviour
        // of Eloquent for dealing with many:many relationships.
        if ($this->isCamelCase() && strpos($key, 'pivot_') === false) {
            $key = camel_case($key);
        }

        return $key;
    }

    /**
     * Determines whether the model (or its parent) requires camelcasing. This is required
     * for pivot models whereby they actually depend on their parents for this feature.
     *
     * @return bool
     */
    public function isCamelCase()
    {
        return $this->enforceCamelCase or (isset($this->parent) && method_exists($this->parent, 'isCamelCase') && $this->parent->isCamelCase());
    }

	/**
	 * If the field names need to be converted so that they can be accessed by camelCase, then we can do that here.
	 *
	 * @param $key
	 * @return string
	 */
	protected function getSnakeKey($key)
	{
		return snake_case($key);
	}

    /**
     * Because we are changing the case of keys and want to use camelCase throughout the application, whenever
     * we do isset checks we need to ensure that we check using snake_case.
     *
     * @param $key
     * @return mixed
     */
    public function __isset($key)
    {
        $key = snake_case($key);

        return parent::__isset($key);
    }
}
