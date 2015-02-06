<?php
namespace Eloquence\Database;

use Eloquence\Database\Traits\CamelCaseModel;
use Eloquence\Database\Traits\UUIDModel;

/**
 * Class Model
 *
 * Have your models extend the model class to include the below traits.
 *
 * @deprecated 1.1.0 to be removed in 1.2.0 Use traits instead.
 * @package Eloquence\Database
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    use CamelCaseModel;
    use UUIDModel;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @TODO put in docs rather than overwriting a property here.
     *
     * @var bool
     */
    public $incrementing = false;
}
