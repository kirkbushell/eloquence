<?php
namespace Tests\Acceptance\CountCache;

use Eloquence\Behaviours\CountCache\CountCacheObserver;
use Tests\Acceptance\AcceptanceTestCase;
use Tests\Acceptance\Models\Comment;
use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class CountCacheTest extends AcceptanceTestCase
{
    private $data = [];

    public function init()
    {
        Comment::observe(new CountCacheObserver);
        Post::observe(new CountCacheObserver);

        $this->data = $this->setupUserAndPost();
    }

	public function testUserCountCache()
    {
        $user = User::first();

        $this->assertEquals(1, $user->postCount);
    }

    public function testComplexCountCache()
    {
        $post = new Post;
        $post->userId = $this->data['user']->id;
        $post->save();

        $comment = new Comment;
        $comment->userId = $this->data['user']->id;
        $comment->postId = $this->data['post']->id;
        $comment->save();

        $this->assertEquals(1, User::first()->commentCount);
        $this->assertEquals(1, Post::first()->commentCount);

        $comment->postId = $post->id;
        $comment->save();

        $this->assertEquals(0, Post::first()->commentCount);
        $this->assertEquals(1, Post::get()[1]->commentCount);
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
