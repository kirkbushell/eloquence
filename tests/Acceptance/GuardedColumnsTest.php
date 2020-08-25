<?php

namespace Tests\Acceptance;

use Tests\Acceptance\Models\GuardedUser;

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
