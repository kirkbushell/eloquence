<?php
namespace Tests\Unit\Behaviours\CountCache;

use Eloquence\Behaviours\CountCache\CountCacheManager;
use Eloquence\Behaviours\CountCache\CountCacheObserver;
use Tests\Acceptance\AcceptanceTestCase;
use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class CountCacheTest extends AcceptanceTestCase
{
	public function testUserCountCache()
    {
        Post::observe(new CountCacheObserver(new CountCacheManager));

        $user = new User;
        $user->firstName = 'Kirk';
        $user->lastName = 'Bushell';
        $user->save();

        $post = new Post;
        $post->userId = $user->id;
        $post->save();

        $user = User::find($user->id);

        $this->assertEquals(1, $user->postCount);
    }
}
