<?php
namespace Tests\Unit\Behaviours\CountCache;

use Eloquence\Behaviours\CountCache\CountCacheManager;
use Illuminate\Support\Facades\DB;
use Tests\Unit\Stubs\CountCache\Comment;
use Tests\Unit\Stubs\CountCache\Post;
use Tests\Unit\Stubs\RealModelStub;
use Tests\Unit\TestCase;

class CountCacheManagerTest extends TestCase
{
    private $manager;

    public function init()
    {
        $this->manager = new CountCacheManager;
    }

    public function testGetTable()
    {
        $this->assertEquals('real_model_stubs', $this->manager->getTable(new RealModelStub));
        $this->assertEquals('real_model_stubs', $this->manager->getTable('Tests\Unit\Stubs\RealModelStub'));
    }

    public function testIncrementRelatedModel()
    {
        $post = new Post;
        $post->user_id = 2;

        DB::shouldReceive('update')->with('UPDATE `users` SET `posts_count` = `posts_count` + 1 WHERE `id` = 2');

        $this->manager->increment($post);
    }

    public function testDecrementRelatedModel()
    {
        $comment = new Comment;
        $comment->post_id = 7;
        $comment->user_id = 1;

        DB::shouldReceive('update')->with('UPDATE `posts` SET `num_comments` = `num_comments` - 1 WHERE `id` = 7')->once();
        DB::shouldReceive('update')->with('UPDATE `users` SET `comment_count` = `comment_count` - 1 WHERE `id` = 1')->once();

        $this->manager->decrement($comment);
    }

    public function testUpdateCache()
    {
        $comment = new Comment;
        $comment->post_id = 1;
        $comment->syncOriginal();
        $comment->post_id = 2;

        DB::shouldReceive('update')->with('UPDATE `posts` SET `num_comments` = `num_comments` - 1 WHERE `id` = 1')->once();
        DB::shouldReceive('update')->with('UPDATE `posts` SET `num_comments` = `num_comments` + 1 WHERE `id` = 2')->once();

        $this->manager->updateCache($comment);
    }
}
