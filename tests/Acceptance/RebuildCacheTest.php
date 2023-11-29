<?php

namespace Tests\Acceptance;

use Tests\Acceptance\Models\Item;
use Tests\Acceptance\Models\Order;
use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class RebuildCacheTest extends AcceptanceTestCase
{
    function test_countCachesCanBeRebuilt()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Post::factory()->count(5)->for($user1)->create();
        Post::factory()->count(2)->for($user2)->create();

        $user1->postCount = 0;
        $user1->save();
        $user2->postCount = 3;
        $user2->save();

        Post::rebuildCountCache();

        $this->assertEquals(5, $user1->fresh()->postCount);
        $this->assertEquals(2, $user2->fresh()->postCount);
    }

    function test_sumCachesCanBeRebuilt()
    {
        $order = Order::factory()->create();
        Item::factory()->count(3)->for($order)->create(['amount' => 10]);

        $order->totalAmount = 50;
        $order->save();

        $this->assertEquals(50, $order->fresh()->totalAmount);

        Item::rebuildSumCache();

        $this->assertEquals(30, $order->fresh()->totalAmount);
    }
}