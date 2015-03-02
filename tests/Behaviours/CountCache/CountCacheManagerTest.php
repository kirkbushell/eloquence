<?php
namespace Tests\Behaviours\CountCache;

use Eloquence\Behaviours\CountCache\CountCacheManager;
use Illuminate\Support\Facades\DB;
use Tests\Stubs\CountCache\Comment;
use Tests\Stubs\CountCache\Post;
use Tests\Stubs\RealModelStub;
use Tests\TestCase;

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
        $this->assertEquals('real_model_stubs', $this->manager->getTable('Tests\Stubs\RealModelStub'));
    }

    public function testIncrementRelatedModel()
    {
        $post = new Post;
        $post->user_id = 2;

        $params = [
            'table' => 'users',
            'countField' => 'posts_count',
            'operation' => '+',
            'key' => 'id',
            'value' => 2
        ];

        DB::shouldReceive('statement')->with('UPDATE :table SET :countField = :countField :operation 1 WHERE :key = :value', $params);

        $this->manager->increment($post);
    }

    public function testDecrementRelatedModel()
    {
        $comment = new Comment;
        $comment->post_id = 7;
        $comment->user_id = 1;

        $firstOperationParams = [
            'table' => 'posts',
            'countField' => 'num_comments',
            'operation' => '-',
            'key' => 'id',
            'value' => 7
        ];

        $secondOperationParams = [
            'table' => 'users',
            'countField' => 'comment_count',
            'operation' => '-',
            'key' => 'id',
            'value' => 1
        ];

        DB::shouldReceive('statement')->with('UPDATE :table SET :countField = :countField :operation 1 WHERE :key = :value', $firstOperationParams)->once();
        DB::shouldReceive('statement')->with('UPDATE :table SET :countField = :countField :operation 1 WHERE :key = :value', $secondOperationParams)->once();

        $this->manager->decrement($comment);
    }

    public function testUpdateCache()
    {
        $comment = new Comment;
        $comment->post_id = 1;
        $comment->syncOriginal();
        $comment->post_id = 2;

        $this->manager->setOriginal($comment->getOriginal());

        $firstOperationParams = [
            'table' => 'posts',
            'countField' => 'num_comments',
            'operation' => '-',
            'key' => 'id',
            'value' => 1
        ];

        $secondOperationParams = [
            'table' => 'posts',
            'countField' => 'num_comments',
            'operation' => '+',
            'key' => 'id',
            'value' => 2
        ];

        DB::shouldReceive('statement')->with('UPDATE :table SET :countField = :countField :operation 1 WHERE :key = :value', $firstOperationParams)->once();
        DB::shouldReceive('statement')->with('UPDATE :table SET :countField = :countField :operation 1 WHERE :key = :value', $secondOperationParams)->once();

        $this->manager->updateCache($comment);
    }
}
