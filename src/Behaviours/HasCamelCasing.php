<?php

namespace Eloquence\Behaviours;

use Illuminate\Support\Str;

trait HasCamelCasing
{
    /**
     * Alter eloquent model behaviour so that model attributes can be accessed via camelCase, but more importantly,
     * attributes also get returned as camelCase fields.
     *
     * @var bool
     */
    public $enforceCamelCase = true;

    /**
     * Overloads the eloquent isGuardableColumn method to ensure that we are checking for the existence of
     * the snake_cased column name.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isGuardableColumn($key)
    {
        return parent::isGuardableColumn($this->getSnakeKey($key));
    }

    /**
     * Overloads the eloquent setAttribute method to ensure that fields accessed
     * in any case are converted to snake_case, which is the defacto standard
     * for field names in databases.
     *
     * @param string $key
     * @param mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        return parent::setAttribute($this->getSnakeKey($key), $value);
    }

    /**
     * Retrieve a given attribute but allow it to be accessed via alternative case methods (such as camelCase).
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key): mixed
    {
        return $this->isRelation($key) ? parent::getAttribute($key) : parent::getAttribute($this->getSnakeKey($key));
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
     * Get the model's relationships, converting field casing if necessary.
     *
     * @return array
     */
    public function relationsToArray()
    {
        return $this->toCamelCase(parent::relationsToArray());
    }

    /**
     * Overloads eloquent's getHidden method to ensure that hidden fields declared
     * in camelCase are actually hidden and not exposed when models are turned
     * into arrays.
     *
     * @return array
     */
    public function getHidden()
    {
        return array_map(Str::class.'::snake', $this->hidden);
    }

    /**
     * Overloads the eloquent getCasts method to ensure that cast field declarations
     * can be made in camelCase but mapped to/from DB in snake_case.
     *
     * @return array
     */
    public function getCasts()
    {
        return collect(parent::getCasts())
            ->mapWithKeys(function ($cast, $key) {
                return [Str::snake($key) => $cast];
            })
            ->toArray();
    }

    /**
     * Converts a given array of attribute keys to the casing required by CamelCased.
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
     * Converts a given array of attribute keys to the casing required by CamelCased.
     *
     * @param $attributes
     * @return array
     */
    public function toSnakeCase($attributes)
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
            $key = Str::camel($key);
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
        return Str::snake($key);
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
        return parent::__isset($key) || parent::__isset($this->getSnakeKey($key));
    }

    /**
     * Because we are changing the case of keys and want to use camelCase throughout the application, whenever
     * we do unset variables we need to ensure that we unset using snake_case.
     *
     * @param $key
     * @return void
     */
    public function __unset($key)
    {
        return parent::__unset($this->getSnakeKey($key));
    }
}