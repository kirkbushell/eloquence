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
        parent::setAttribute($this->getTrueKey($key), $value);
    }

    /**
     * Retrieve a given attribute but allow it to be accessed via alternative case methods (such as camelCase).
     *
     * @param  string $key
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
        return $this->convertAttributesToTrueCase(parent::attributesToArray());
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
        return $this->convertAttributesToTrueCase(parent::relationsToArray());
    }

    /**
     * Converts a given array of attribute keys to the casing required by CamelCaseModel.
     *
     * @param $attributes
     * @return array
     */
    private function convertAttributesToTrueCase($attributes)
    {
        $convertedAttributes = [];

        foreach ($attributes as $key => $value) {
            $key = $this->trueKeyName($key);

            $convertedAttributes[$key] = $value;
        }

        return $convertedAttributes;
    }

    /**
     * Retrieves the true key name for a key.
     *
     * @param $key
     * @return string
     */
    protected function trueKeyName($key)
    {
        if ($this->isCamelCase()) {
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
	protected function getTrueKey($key)
	{
		return snake_case($key);
	}

    /**
     * Fill the model with an array of attributes and properly translates keys
     * to snake_case when using Model::fill($attributes) and returns the instance
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */

    public function fill(array $attributes)
    {
        $new_attributes = [];

        array_walk($attributes, function ($value, $key) use (&$new_attributes) {
            $new_attributes[$this->getTrueKey($key)] = $value;
        });

        return parent::fill($new_attributes);
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
