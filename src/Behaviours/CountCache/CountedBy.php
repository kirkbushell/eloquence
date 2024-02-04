<?php

namespace Eloquence\Behaviours\CountCache;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class CountedBy
{
    public function __construct(readonly ?string $as = null)
    {
    }
}
