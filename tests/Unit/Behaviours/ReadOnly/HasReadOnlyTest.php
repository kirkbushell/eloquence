<?php

namespace Tests\Unit\Behaviours\ReadOnly;

use Eloquence\Behaviours\ReadOnly\WriteAccessDenied;
use Tests\Unit\Stubs\ReadOnlyModelStub;
use Tests\Unit\TestCase;

final class HasReadOnlyTest extends TestCase
{
    function test_attributes_cannot_be_set()
    {
        $this->expectException(WriteAccessDenied::class);

        new ReadOnlyModelStub(['value' => 1]);
    }

    function test_model_cannot_be_saved()
    {
        $this->expectException(WriteAccessDenied::class);

        $model = new ReadOnlyModelStub;
        $model->save();
    }
}