<?php
namespace Tests\Acceptance;

use Eloquence\Behaviours\Slugged\SlugObserver;
use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class SluggedTest extends AcceptanceTestCase
{
	public function testUserSlug()
    {
        User::observe(new SlugObserver);

        $user = new User;
        $user->firstName = 'Kirk';
        $user->lastName = 'Bushell';
        $user->save();

        $this->assertEquals('kirk-bushell', $user->slug);
    }

    public function testPostSlug()
    {
        Post::observe(new SlugObserver);

        $post = new Post;
        $post->save();

        $this->assertRegExp('/^[a-z0-9]{8}$/i', $post->slug);
    }
}
