<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CamelCased;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use CamelCased;

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
