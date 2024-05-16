<?php
namespace Tests\Acceptance;

use Tests\Acceptance\Models\Item;
use Tests\Acceptance\Models\Order;

class SumCacheTest extends AcceptanceTestCase
{
    function test_relatedModelSumCacheIsIncreasedWhenModelIsCreated()
    {
        Item::factory()->create(['amount' => 34]);

        $this->assertEquals(34, Order::first()->totalAmount);
    }

    function test_relatedModelSumCacheIsDecreasedWhenModelIsDeleted()
    {
        $item = Item::factory()->create(['amount' => 19]);
        $item->delete();

        $this->assertEquals(0, Order::first()->totalAmount);
    }

    function test_whenAnAggregatedModelValueSwitchesContext()
    {
        $item = Item::factory()->create();
        $newOrder = Order::factory()->create();

        $item = $item->fresh();
        $item->orderId = $newOrder->id;
        $item->save();

        $this->assertEquals(0, Order::first()->totalAmount);
        $this->assertEquals($item->amount, $newOrder->fresh()->totalAmount);
    }

    function test_aggregateValuesAreUpdatedWhenModelsAreRestored()
    {
        $item = Item::factory()->create();
        $item->delete(); // Triggers decrease in order total
        $item->restore(); // Restores order total

        $this->assertEquals($item->amount, Order::first()->totalAmount);
    }

    function test_aggregateValueIsSetToCorrectAmountWhenSourceFieldChanges()
    {
        $item = Item::factory()->create();
        $item->amount = 20;
        $item->save();

        $this->assertEquals(20, Order::first()->totalAmount);
    }

    function test_aggregateValueOnOriginalRelatedModelIsUpdatedCorrectlyWhenTheForeignKeyAndAmountIsChanged()
    {
        $item = Item::factory()->create();

        $newOrder = Order::factory()->create();

        $item = $item->fresh();
        $item->amount = 20;
        $item->orderId = $newOrder->id;
        $item->save();

        $this->assertEquals(0, Order::first()->totalAmount);
        $this->assertEquals(20, $newOrder->fresh()->totalAmount);
    }

    public function test_cacheIsNotUsedWhenRelatedFieldIsNull()
    {
        $order = Order::factory()->create();
        $items = Item::factory()->count(5)->for($order)->create(['amount' => 1]);

        $this->assertEquals(5, Order::first()->totalAmount);

        $items->first()->order_id = null;
        $items->first()->save();

        $this->assertEquals(4, $order->fresh()->totalAmount);
    }
}