<?php

namespace Eloquence\Database;

use Eloquence\Database\CamelCaseModel;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    use CamelCaseModel;
}
