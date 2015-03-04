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

    public function __construct(array $attributes = [])
    {
        if(!empty($attributes)) {
            $this->attributes = [];
            $this->fill($attributes);
        }
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value)
        {
            $this->setAttribute($key, $value);
        }

    }

    public function attributesToArray()
    {
        return $this->attributes;
    }

    public function rawAttributesToArray()
    {
        return $this->attributes;
    }

    public function getAttribute($key)
    {
        return $this->attributes[$key];
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public static function create(array $attributes)
    {
        return new static($attributes);
    }
}
