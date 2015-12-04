<?php
namespace Tests\Acceptance\Models;

use Eloquence\Database\Traits\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use CamelCasing;

    public function items()
    {
        return $this->hasMany('Tests\Acceptance\Models\Item');
    }

}
