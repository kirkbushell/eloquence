<?php
namespace Tests\Acceptance;

use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class HasSlugsTest extends AcceptanceTestCase
{
    function test_slugsCanBeGeneratedWithCustomStrategy()
    {
        $user = User::factory(['firstName' => 'Kirk', 'lastName' => 'Bushell'])->create();

        $this->assertEquals('kirk-bushell', (string) $user->slug);
    }

    function test_slugsCanBeGeneratedUsingRandomValues()
    {
        $post = Post::factory()->create();

        $this->assertMatchesRegularExpression('/^[a-z0-9]{8}$/i', (string) $post->slug);
    }
}
