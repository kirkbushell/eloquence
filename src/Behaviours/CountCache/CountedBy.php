<?php

namespace Eloquence\Behaviours\CountCache;

#[\Attribute]
class CountedBy
{
    public function __construct(readonly ?string $as = null) {}
}