<?php

namespace Tests\Acceptance;

use Tests\Acceptance\Models\Item;
use Tests\Acceptance\Models\Order;
use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class RebuildCachesCommandTest extends AcceptanceTestCase
{
    function test_itCanRebuildCachesOfAllAffectedModels()
    {
        $order1 = Order::factory()->create(['total_amount' => 0]);
        $order2 = Order::factory()->create(['total_amount' => 0]);

        Item::factory()->for($order1)->count(10)->create(['amount' => 10]);
        Item::factory()->for($order2)->count(5)->create(['amount' => 5]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Post::factory()->for($user1)->count(10)->create();
        Post::factory()->for($user2)->count(5)->create();

        $order1->totalAmount = 0;
        $order1->save();

        $order2->totalAmount = 0;
        $order2->save();

        $result = $this->artisan('eloquence:rebuild-caches '.__DIR__.'/../../tests/Acceptance/Models');

        $result->assertExitCode(0);

        $this->assertDatabaseHas('users', ['post_count' => 10]);
        $this->assertDatabaseHas('users', ['post_count' => 5]);
        $this->assertDatabaseHas('orders', ['total_amount' => 100]);
        $this->assertDatabaseHas('orders', ['total_amount' => 25]);
    }
}