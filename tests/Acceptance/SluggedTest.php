<?php
namespace Tests\Acceptance;

use Tests\Acceptance\Models\Post;
use Tests\Acceptance\Models\User;

class SluggedTest extends AcceptanceTestCase
{
    function test_slugsCanBeGeneratedWithCustomStrategy()
    {
        $user = new User;
        $user->firstName = 'Kirk';
        $user->lastName = 'Bushell';
        $user->save();

        $this->assertEquals('kirk-bushell', (string) $user->slug);
    }

    function test_slugsCanBeGeneratedUsingRandomValues()
    {
        $user = new User;
        $user->firstName = 'Kirk';
        $user->lastName = 'Bushell';
        $user->save();

        $post = new Post;
        $post->userId = $user->id;
        $post->save();

        $this->assertMatchesRegularExpression('/^[a-z0-9]{8}$/i', (string) $post->slug);
    }
}
