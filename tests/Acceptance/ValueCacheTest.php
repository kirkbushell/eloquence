<?php

namespace Tests\Acceptance;

use Tests\Acceptance\Models\Category;
use Tests\Acceptance\Models\Post;

class ValueCacheTest extends AcceptanceTestCase
{
    function test_values_from_related_models_are_cached()
    {
        $category = Category::factory()->create();

        $this->assertNull($category->last_activity_at);

        $post = Post::factory()->create(['category_id' => $category->id, 'publish_at' => now()->subDays(mt_rand(1, 10))]);

        $this->assertEquals($category->fresh()->last_activity_at, $post->publish_at);
    }
}
