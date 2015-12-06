<?php
namespace tests\Acceptance;

use tests\Acceptance\Models\Post;
use tests\Acceptance\Models\User;

class SluggedTest extends AcceptanceTestCase
{
    public function testUserSlug()
    {
        $user = new User;
        $user->firstName = 'Kirk';
        $user->lastName = 'Bushell';
        $user->save();

        $this->assertEquals('kirk-bushell', (string) $user->slug);
    }

    public function testPostSlug()
    {
        $post = new Post;
        $post->save();

        $this->assertRegExp('/^[a-z0-9]{8}$/i', (string) $post->slug);
    }
}
