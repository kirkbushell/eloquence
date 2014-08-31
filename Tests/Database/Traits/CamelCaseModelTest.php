<?php

namespace Tests\Database\Traits;

use Tests\Stubs\ModelStub;

class CamelCaseModelTest extends \PHPUnit_Framework_TestCase
{
    public function testAttributesRetrieval()
    {
        $model = new ModelStub;
        $attributes = $model->attributesToArray();

        $this->assertArrayHasKey('firstName', $attributes);
        $this->assertArrayHasKey('lastName', $attributes);
        $this->assertArrayHasKey('address', $attributes);
        $this->assertArrayHasKey('firstName', $attributes);
    }
}
