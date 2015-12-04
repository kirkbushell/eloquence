<?php
namespace Eloquence\Database;

use Eloquence\Database\Traits\CamelCasing;
use Eloquence\Database\Traits\Uuid;

/**
 * Class Model
 *
 * Have your models extend the model class to include the below traits.
 *
 * @package Eloquence\Database
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    use CamelCasing;
    use Uuid;
}
