<?php

namespace Tests\Database\Traits;

use Tests\Stubs\ModelStub;

class CamelCaseModelTest extends \PHPUnit_Framework_TestCase
{
    private $model;

    public function setUp()
    {
        $this->model = new ModelStub;
    }

    public function testAttributesAsArray()
    {
        $attributes = $this->model->attributesToArray();

        $this->assertArrayHasKey('firstName', $attributes);
        $this->assertArrayHasKey('lastName', $attributes);
        $this->assertArrayHasKey('address', $attributes);
        $this->assertArrayHasKey('firstName', $attributes);
    }

    public function testAttributeDeclaration()
    {
        $this->model->setAttribute('firstName', 'Andrew');

        $this->assertEquals('Andrew', $this->model->getAttribute('firstName'));
    }

    public function testAttributeRetrieval()
    {
        $this->assertEquals('Kirk', $this->model->getAttribute('firstName'));
    }
}
