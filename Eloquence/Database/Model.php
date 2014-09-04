<?php

namespace Eloquence\Database;

use Eloquence\Database\CamelCaseModel;
use Eloquence\Database\Traits\UUIDModel;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    use CamelCaseModel;
    use UUIDModel;
}
