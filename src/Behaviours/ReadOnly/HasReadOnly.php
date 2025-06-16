<?php

namespace Eloquence\Behaviours\ReadOnly;

trait HasReadOnly
{
    public function setAttribute($key, $value)
    {
        throw new WriteAccessDenied(get_class($this));
    }

    public function save(array $options = [])
    {
        throw new WriteAccessDenied(get_class($this));
    }
}