<?php
namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\SumCache\HasSums;
use Eloquence\Database\Model;

class Item extends Model
{
    use HasSums;

    public function sumCaches(): array
    {
        return [
            'Tests\Unit\Stubs\Order',
            [
                'model' => 'Tests\Unit\Stubs\Order',
                'sumField' => 'itemTotalExplicit',
                'columnToSum' => 'total',
                'foreignKey' => 'itemId',
                'key' => 'id',
            ]
        ];
    }
}