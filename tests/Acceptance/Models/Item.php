<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\SumCache\Summable;
use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use CamelCasing;
    use Summable;

    public function sumCaches()
    {
        return [
            'Tests\Acceptance\Models\Order',
            [
                'model' => 'Tests\Acceptance\Models\Order',
                'field' => 'itemTotalExplicit',
                'columnToSum' => 'total',
                'foreignKey' => 'orderId',
                'key' => 'id',
            ]
        ];
    }
}
