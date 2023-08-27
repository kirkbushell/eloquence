<?php

namespace Tests\Acceptance;

use Tests\Acceptance\Models\Category;
use Tests\Acceptance\Models\Comment;

class ChainedAggregatesTest extends AcceptanceTestCase
{
    function test_aggregateDependentsAreUpdated()
    {
        // Comment will create a user, and a post - the post created will bubble up to category and update it as well.
        Comment::factory()->create();

        $this->assertSame(1, Category::first()->postCount);
        $this->assertSame(1, Category::first()->totalComments);
    }
}