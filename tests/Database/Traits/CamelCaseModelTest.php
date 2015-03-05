<?php
namespace Tests\Database\Traits;

use Tests\Stubs\ModelStub;
use Tests\Stubs\PivotModelStub;
use Tests\Stubs\RealModelStub;
use Tests\TestCase;

class CamelCaseModelTest extends TestCase
{
    private $model;

    public function init()
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

    public function testArrayRetrievalOfAttributes()
    {
        $expectedArray = [
            'firstName' => 'Kirk',
            'lastName' => 'Bushell',
            'address' => 'Home',
            'countryOfOrigin' => 'Australia'
        ];

        $actualArray = $this->model->getAttributes();

        $this->assertEquals($expectedArray, $actualArray);
    }

    public function testAttributeConversionOfAllAttributes()
    {
        $expectedAttributes = [
            'address' => 'Home',
            'countryOfOrigin' => 'Australia',
            'firstName' => 'Kirk',
            'lastName' => 'Bushell'
        ];

        $this->assertEquals($expectedAttributes, $this->model->attributesToArray());
    }

    public function testAttributeConversionLeavesPivotFieldsAlone()
    {
        $model = new PivotModelStub;

        $expectedAttributes = [
            'firstName' => 'Kirk',
            'pivot_field' => 'whatever'
        ];

        $this->assertEquals($expectedAttributes, $model->attributesToArray());
    }

    public function testModelFilling()
    {
        $model = new RealModelStub(['myField' => 'value']);
        $model2 = new RealModelStub(['my_field' => 'value']);

        $this->assertEquals($model->myField, 'value');
        $this->assertEquals($model2->myField, 'value');
    }
}
