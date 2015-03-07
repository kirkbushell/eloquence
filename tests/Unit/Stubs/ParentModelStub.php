<?php
namespace Tests\Unit\Stubs;

class ParentModelStub
{
    public function attributesToArray()
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
}
