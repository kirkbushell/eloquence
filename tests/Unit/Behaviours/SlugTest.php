<?php
namespace tests\Unit\Database\Traits;

use Eloquence\Behaviours\Slug;
use Tests\Unit\TestCase;

class SlugTest extends TestCase
{
    public function test_random_slug_is_random()
    {
        $this->assertNotEquals(Slug::random(), Slug::random());
    }

    public function test_slugs_are_8_characters_long()
    {
        $this->assertEquals(8, strlen((string) Slug::random()));
    }
}
