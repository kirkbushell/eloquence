<?php

namespace Tests\Stubs;

class ParentModelStub
{
    protected $attributes = [
        'first_name' => 'Kirk',
        'last_name' => 'Bushell',
        'address' => 'Home',
        'country_of_origin' => 'Australia'
    ];

    public function attributesToArray()
    {
        return $this->attributes;
    }

    public function getAttribute($key)
    {
        return $this->attributes[$key];
    }
}
