<?php
namespace Tests\Acceptance;

use Tests\Acceptance\Models\Comment;
use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class CountCacheTest extends AcceptanceTestCase
{
    function test_userHasASinglePostCount()
    {
        Post::factory()->create();

        $this->assertEquals(1, User::first()->postCount);
    }

    function test_whenRelatedModelsAreSwitchedBothCountCachesAreUpdated()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $posts = Post::factory()->count(2)->for($user1)->create();
        $comment = Comment::factory()->for($user1)->for($posts->first())->create();

        $this->assertEquals(2, $user1->fresh()->postCount);
        $this->assertEquals(1, $user1->fresh()->commentCount);
        $this->assertEquals(1, $posts->first()->fresh()->commentCount);

        $comment = $comment->fresh();
        $comment->userId = $user2->id;
        $comment->save();

        $this->assertEquals(0, $user1->fresh()->commentCount);
        $this->assertEquals(1, $user2->fresh()->commentCount);
    }

    public function testItCanHandleModelRestoration()
    {
        $post = Post::factory()->create();

        $comment = Comment::factory()->for($post)->create();
        $comment->delete();
        $comment->restore();

        $this->assertEquals(1, $post->fresh()->commentCount);
    }
}
