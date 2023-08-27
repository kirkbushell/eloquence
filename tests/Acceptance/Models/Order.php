<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CamelCased;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use CamelCased;
    use HasFactory;

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    protected static function newFactory(): Factory
    {
        return OrderFactory::new();
    }
}
