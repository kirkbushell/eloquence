<?php
namespace Tests\Acceptance;

use Tests\Acceptance\Models\Item;
use Tests\Acceptance\Models\Order;

class SumCacheTest extends AcceptanceTestCase
{
    private array $data = [];

    public function init()
    {
        $order = new Order;
        $order->save();

        $item = new Item;
        $item->amount = 34;
        $item->orderId = $order->id;
        $item->save();

        $this->data = compact('order', 'item');
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
        $newOrder = new Order;
        $newOrder->save();

        $item = new Item;
        $item->orderId = $this->data['order']->id;
        $item->amount = 45;
        $item->save();

        $item = $item->fresh();
        $item->orderId = $newOrder->id;
        $item->save();

        $this->assertEquals(34, $this->data['order']->fresh()->totalAmount);
        $this->assertEquals(45, $newOrder->fresh()->totalAmount);
    }

    function test_aggregateValuesAreUpdatedWhenModelsAreRestored()
    {
        $this->data['item']->delete();

        $this->assertEquals(0, $this->data['order']->fresh()->totalAmount);
    }

    function test_aggregateValueIsSetToCorrectAmountWhenSourceFieldChanges()
    {
        $item = $this->data['item'];

        $item->amount = 20;
        $item->save();

        $this->assertEquals(20, $this->data['order']->fresh()->totalAmount);
    }

    function test_aggregateValueOnOriginalRelatedModelIsUpdatedCorrectlyWhenTheForeignKeyAndAmountIsChanged()
    {
        $item = $this->data['item'];

        $newOrder = new Order;
        $newOrder->save();

        $item = $item->fresh();
        $item->amount = 20;
        $item->orderId = $newOrder->id;
        $item->save();

        $this->assertEquals(0, $this->data['order']->fresh()->totalAmount);
        $this->assertEquals(20, $newOrder->fresh()->totalAmount);
    }
}
