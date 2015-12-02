<?php
namespace Tests\Unit\Stubs\SumCache;

use Eloquence\Behaviours\SumCache\SumCache;
use Eloquence\Database\Model;

class Item extends Model implements SumCache
{
    public function sumCaches()
    {
        return [
            'Tests\Unit\Stubs\SumCache\Order',
            [
                'model' => 'Tests\Unit\Stubs\SumCache\Order',
                'sumField' => 'itemTotalExplicit',
                'columnToSum' => 'total',
                'foreignKey' => 'itemId',
                'key' => 'id',
            ]
        ];
    }
}
