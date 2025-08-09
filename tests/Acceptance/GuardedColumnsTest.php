<?php

namespace Tests\Acceptance;

use PHPUnit\Framework\Attributes\Group;
use Tests\Acceptance\Models\GuardedUser;

#[Group('acceptance')]
class GuardedColumnsTest extends AcceptanceTestCase
{
    public function testGuardedUser()
    {
        $user = GuardedUser::create([
            'firstName' => 'Stuart',
            'last_name' => 'Jones',
        ]);

        $this->assertEquals('Stuart', $user->firstName);
        $this->assertEquals('Jones', $user->lastName);
    }
}