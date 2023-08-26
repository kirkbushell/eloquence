<?php
namespace Tests\Acceptance;

use Tests\Acceptance\Models\Comment;
use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class CountCacheTest extends AcceptanceTestCase
{
    private $data = [];

    public function init()
    {
        $this->data = $this->setupUserAndPost();
    }

    function test_userHasASinglePostCount()
    {
        $user = User::first();

        $this->assertEquals(1, $user->postCount);
    }

    function test_whenRelatedModelsAreSwitchedBothCountCachesAreUpdated()
    {
        $post = new Post;
        $post->userId = $this->data['user']->id;
        $post->save();

        $comment = new Comment;
        $comment->userId = $this->data['user']->id;
        $comment->postId = $this->data['post']->id;
        $comment->save();

        $this->assertEquals(2, User::first()->postCount);
        $this->assertEquals(1, User::first()->commentCount);
        $this->assertEquals(1, Post::first()->commentCount);

        $comment = $comment->fresh();
        $comment->postId = $post->id;
        $comment->save();

        $this->assertEquals(0, $this->data['post']->fresh()->commentCount);
        $this->assertEquals(1, $post->fresh()->commentCount);
    }

    public function testItCanHandleNegativeCounts()
    {
        $post = new Post;
        $post->userId = $this->data['user']->id;
        $post->save();

        $comment = new Comment;
        $comment->userId = $this->data['user']->id;
        $comment->postId = $this->data['post']->id;
        $comment->save();
        $comment->delete();
        $comment->restore();

        $this->assertEquals(1, Post::first()->commentCount);
    }

    private function setupUserAndPost()
    {
        $user = new User;
        $user->firstName = 'Kirk';
        $user->lastName = 'Bushell';
        $user->save();

        $post = new Post;
        $post->userId = $user->id;
        $post->save();

        return compact('user', 'post');
    }
}
