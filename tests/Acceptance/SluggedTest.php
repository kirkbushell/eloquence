<?php
namespace Tests\Acceptance;

use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class SluggedTest extends AcceptanceTestCase
{
    public function testUserSlug()
    {
        User::$slugAttempts = 0;
        
        $user = new User;
        $user->firstName = 'Kirk';
        $user->lastName = 'Bushell';
        $user->save();

        $this->assertEquals('kirk-bushell', (string) $user->slug);

        $user = new User;
        $user->firstName = 'Kirk';
        $user->lastName = 'Bushell';
        $user->save();

        $this->assertEquals('kirk-bushell-1', (string) $user->slug);
    }

    public function testPostSlug()
    {
        $post = new Post;
        $post->save();

        $this->assertRegExp('/^[a-z0-9]{8}$/i', (string) $post->slug);
    }
}
