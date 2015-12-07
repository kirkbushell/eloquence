<?php
namespace Tests\Acceptance;

use Tests\Acceptance\Models\Item;
use Tests\Acceptance\Models\Order;

class SumCacheTest extends AcceptanceTestCase
{
    private $data = [];

    public function init()
    {
        $this->data = $this->setupOrderAndItem();
    }

    public function testOrderSumCache()
    {
        $order = Order::first();

        $this->assertEquals(34, $order->itemTotal);
        $this->assertEquals(34, $order->itemTotalExplicit);
    }

    public function testAdditionalSumCache()
    {
        $order = new Order;
        $order->save();

        $item = new Item;
        $item->orderId = $this->data['order']->id;
        $item->total = 45;
        $item->save();

        $this->assertEquals(79, Order::first()->itemTotal);
        $this->assertEquals(0,  Order::get()[1]->itemTotal);

        $this->assertEquals(79, Order::first()->itemTotalExplicit);
        $this->assertEquals(0,  Order::get()[1]->itemTotalExplicit);

        $item->orderId = $order->id;
        $item->save();

        $this->assertEquals(34, Order::first()->itemTotal);
        $this->assertEquals(45, Order::get()[1]->itemTotal);

        $this->assertEquals(34, Order::first()->itemTotalExplicit);
        $this->assertEquals(45, Order::get()[1]->itemTotalExplicit);
    }

    private function setupOrderAndItem()
    {
        $order = new Order;
        $order->save();

        $item = new Item;
        $item->total = 34;
        $item->orderId = $order->id;
        $item->save();

        return compact('order', 'item');
    }
}
