<?php
namespace Tests\Unit\Behaviours\SumCache;

use Eloquence\Behaviours\SumCache\SumCacheManager;
use Illuminate\Support\Facades\DB;
use Tests\Unit\Stubs\SumCache\Item;
use Tests\Unit\Stubs\SumCache\Order;
use Tests\Unit\Stubs\RealModelStub;
use Tests\Unit\TestCase;

class SumCacheManagerTest extends TestCase
{
    private $manager;

    public function init()
    {
        $this->manager = new SumCacheManager;
    }

    public function testGetTable()
    {
        $this->assertEquals('real_model_stubs', $this->manager->getTable(new RealModelStub));
        $this->assertEquals('real_model_stubs', $this->manager->getTable('Tests\Unit\Stubs\RealModelStub'));
    }

    public function testIncrementRelatedModel()
    {
        $item = new Item;
        $item->total = 100;
        $item->order_id = 2;

        DB::shouldReceive('update')->with('UPDATE `orders` SET `item_total` = `item_total` + (100) WHERE `id` = 2');

        $this->manager->increment($item);
    }

    public function testDecrementRelatedModel()
    {
        $item = new Item;
        $item->total = 100;
        $item->order_id = 2;

        DB::shouldReceive('update')->with('UPDATE `orders` SET `item_total` = `item_total` - (100) WHERE `id` = 2')->once();

        $this->manager->decrement($item);
    }

    public function testUpdateCache()
    {
        $item = new Item;
        $item->total = 100;
        $item->order_id = 1;
        $item->syncOriginal();
        $item->order_id = 2;

        DB::shouldReceive('update')->with('UPDATE `orders` SET `item_total` = `item_total` - (100) WHERE `id` = 1')->once();
        DB::shouldReceive('update')->with('UPDATE `orders` SET `item_total` = `item_total` + (100) WHERE `id` = 2')->once();

        $this->manager->updateCache($item);
    }
}
