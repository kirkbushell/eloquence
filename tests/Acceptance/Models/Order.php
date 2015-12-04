<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use \Eloquence\Behaviours\CamelCasing;

    public function items()
    {
        return $this->hasMany('Tests\Acceptance\Models\Item');
    }
}
