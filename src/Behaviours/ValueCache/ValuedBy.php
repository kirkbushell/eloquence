<?php

namespace Eloquence\Behaviours\ValueCache;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ValuedBy
{
    public function __construct(readonly string $from, readonly ?string $as)
    {
    }
}
