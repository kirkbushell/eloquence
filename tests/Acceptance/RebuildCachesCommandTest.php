<?php

namespace Tests\Acceptance;

class RebuildCachesCommandTest extends AcceptanceTestCase
{
    function test_itCanRebuildCachesOfAllAffectedModels()
    {
        $result = $this->artisan('eloquence:rebuild-caches '.__DIR__.'/../../tests/Acceptance/Models');

        $result->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'post_count' => 5,
        ]);

        $this->assertDatabaseHas('users', [
            'post_count' => 2,
        ]);

        $this->assertDatabaseHas('orders', [
            'total_amount' => 30,
        ]);
    }
}