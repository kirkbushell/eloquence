<?php
namespace Tests\Unit\Stubs\SumCache;

use Eloquence\Behaviours\SumCache\HasSums;
use Eloquence\Database\Model;

class Item extends Model
{
    use HasSums;

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
