<?php

namespace Eloquence\Behaviours\SumCache;

#[\Attribute]
class SummedBy
{
    public function __construct(readonly string $from, readonly string $as) {}
}
