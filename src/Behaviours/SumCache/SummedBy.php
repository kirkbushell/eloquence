<?php

namespace Eloquence\Behaviours\SumCache;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class SummedBy
{
    public function __construct(readonly string $from, readonly string $as)
    {
    }
}
