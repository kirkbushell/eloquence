<?php
namespace Tests\Acceptance;

use Eloquence\Behaviours\SumCache\SumCacheObserver;
use Tests\Acceptance\Models\Item;
use Tests\Acceptance\Models\Order;

class SumCacheTest extends AcceptanceTestCase
{
    private $data = [];

    public function init()
    {
        Item::observe(new SumCacheObserver);

        $this->data = $this->setupOrderAndItem();
    }

	public function testOrderCountCache()
    {
        $order = Order::first();

        $this->assertEquals(100, $order->itemTotal);
    }

    public function testAdditionalSumCache()
    {
        $firstOrder = Order::first();
        $secondOrder = new Order;
        $secondOrder->save();

        $item = new Item;
        $item->orderId = $secondOrder->id;
        $item->total = 23;
        $item->save();

        $this->assertEquals(100, Order::first()->itemTotal);
        $this->assertEquals(23,  Order::get()[1]->itemTotal);

        $item->orderId = $firstOrder->id;
        $item->save();

        $this->assertEquals(123, Order::first()->itemTotal);
        $this->assertEquals(0,   Order::get()[1]->itemTotal);

    }

    private function setupOrderAndItem()
    {
        $order = new Order;
        $order->save();

        $item = new Item;
        $item->total = 100;
        $item->orderId = $order->id;
        $item->save();

        return compact('order', 'item');
    }
}
