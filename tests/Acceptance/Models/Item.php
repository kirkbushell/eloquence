<?php
namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\SumCache\SumCache;
use Eloquence\Database\Traits\CamelCaseModel;
use Illuminate\Database\Eloquent\Model;

class Item extends Model implements SumCache
{
    use CamelCaseModel;

    public function sumCaches()
    {
        return [
            'Tests\Acceptance\Models\Order',
        ];
    }
}
