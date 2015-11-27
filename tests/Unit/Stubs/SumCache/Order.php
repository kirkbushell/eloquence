<?php
namespace Tests\Unit\Stubs\SumCache;

use Eloquence\Database\Traits\SluggableModel;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use SluggableModel;

}
