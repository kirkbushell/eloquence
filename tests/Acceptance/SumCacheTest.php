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

    function test_relatedModelSumCacheIsIncreasedWhenModelIsCreated()
    {
        $order = Order::first();

        $this->assertEquals(34, $order->totalAmount);
    }

    function test_relatedModelSumCacheIsDecreasedWhenModelIsDeleted()
    {
        $this->data['item']->delete();

        $order = Order::first();

        $this->assertEquals(0, $order->totalAmount);
    }

    function test_whenAnAggregatedModelValueSwitchesContext()
    {
        $order = new Order;
        $order->save();

        $item = new Item;
        $item->orderId = $this->data['order']->id;
        $item->amount = 45;
        $item->save();

        $item = $item->fresh();
        $item->orderId = $order->id;
        $item->save();

        $this->assertEquals(34, $this->data['order']->fresh()->totalAmount);
        $this->assertEquals(45, $order->fresh()->totalAmount);
    }

    function test_aggregateValuesAreUpdatedWhenModelsAreRestored()
    {
        $this->data['item']->delete();

        $this->assertEquals(0, $this->data['order']->fresh()->totalAmount);
    }

    private function setupOrderAndItem()
    {
        $order = new Order;
        $order->save();

        $item = new Item;
        $item->amount = 34;
        $item->orderId = $order->id;
        $item->save();

        return compact('order', 'item');
    }
}
