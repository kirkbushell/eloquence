<?php
namespace Eloquence\Database;

use Eloquence\Database\Traits\CamelCaseModel;
use Eloquence\Database\Traits\UUIDModel;

/**
 * Class Model
 *
 * Have your models extend the model class to include the below traits.
 *
 * @package Eloquence\Database
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    use CamelCaseModel;
    use UUIDModel;
}
