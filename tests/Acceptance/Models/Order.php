<?php
namespace Tests\Acceptance\Models;

use Eloquence\Database\Traits\CamelCaseModel;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use CamelCaseModel;

    public function items()
    {
        return $this->hasMany('Tests\Acceptance\Models\Item');
    }

}
