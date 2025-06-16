<?php

namespace Eloquence\Behaviours\ReadOnly;

final class WriteAccessDenied extends \RuntimeException
{
    public function __construct(string $model)
    {
        $this->message = "Write access denied for model {$model}.";
    }
}