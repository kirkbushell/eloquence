<?php
namespace Tests\Stubs;

/**
 * Class BaseModel
 *
 * Base class to help support the camel case model trait tests.
 *
 * @package Tests\Stubs
 */
abstract class BaseModel
{
    public $attributes = ['first_name' => 'Kirk', 'last_name' => 'Bushell'];

	public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute($key)
    {
        return $this->attributes[$key];
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function attributesToArray()
    {
        return $this->attributes;
    }
}
