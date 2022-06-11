<?php
namespace Tests\Acceptance;

use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

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

        $this->assertMatchesRegularExpression('/^[a-z0-9]{8}$/i', (string) $post->slug);
    }
}
