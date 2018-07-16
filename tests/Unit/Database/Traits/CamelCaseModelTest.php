<?php
namespace tests\Unit\Database\Traits;

use Carbon\Carbon;
use Tests\Unit\Stubs\ModelStub;
use Tests\Unit\Stubs\PivotModelStub;
use Tests\Unit\Stubs\RealModelStub;
use Tests\Unit\TestCase;

class CamelCaseModelTest extends TestCase
{
    private $model;

    public function init()
    {
        date_default_timezone_set('Australia/Sydney');

        $this->model = new ModelStub;
    }

    public function test_attributes_as_array()
    {
        $attributes = $this->model->attributesToArray();

        $this->assertArrayHasKey('firstName', $attributes);
        $this->assertArrayHasKey('lastName', $attributes);
        $this->assertArrayHasKey('address', $attributes);
        $this->assertArrayHasKey('firstName', $attributes);
    }

    public function test_attribute_declaration()
    {
        $this->model->setAttribute('firstName', 'Andrew');

        $this->assertEquals('Andrew', $this->model->getAttribute('firstName'));
    }

    public function test_attribute_retrieval()
    {
        $this->assertEquals('Kirk', $this->model->getAttribute('firstName'));
    }

    public function test_attribute_conversion()
    {
        $expectedAttributes = [
            'address' => 'Home',
            'countryOfOrigin' => 'Australia',
            'firstName' => 'Kirk',
            'lastName' => 'Bushell'
        ];

        $this->assertEquals($expectedAttributes, $this->model->attributesToArray());
    }

    public function test_attribute_conversion_leaves_pivots()
    {
        $model = new PivotModelStub;

        $expectedAttributes = [
            'firstName' => 'Kirk',
            'pivot_field' => 'whatever'
        ];

        $this->assertEquals($expectedAttributes, $model->attributesToArray());
    }

    public function test_model_filling()
    {
        $model = new RealModelStub([
            'myField' => 'value',
            'anotherField' => 'yeah',
            'someField' => 'whatever'
        ]);

        $this->assertEquals($model->myField, 'value');
        $this->assertEquals($model->anotherField, 'yeah');
        $this->assertNull($model->someField);
    }

    public function test_isset_unset()
    {
        $model = new RealModelStub;

        // initial check
        $this->assertFalse(isset($model->my_field) || isset($model->myField));

        // snake_case set
        $model->my_field = 'value';
        $this->assertTrue(isset($model->my_field) && isset($model->myField));

        // snake_case unset
        unset($model->my_field);
        $this->assertFalse(isset($model->my_field) || isset($model->myField));

        // camelCase set
        $model->myField = 'value';
        $this->assertTrue(isset($model->my_field) && isset($model->myField));

        // camelCase unset
        unset($model->myField);
        $this->assertFalse(isset($model->my_field) || isset($model->myField));
    }

    public function test_model_hidden_fields()
    {
        $model = new RealModelStub([
            'myField' => 'value',
            'anotherField' => 'yeah',
            'someField' => 'whatever',
            'hiddenField' => 'secrets!',
            'passwordHash' => '1234',
        ]);

        $modelArray = $model->toArray();

        $this->assertFalse(isset($modelArray['hiddenField']));
        $this->assertFalse(isset($modelArray['passwordHash']));

        $this->assertEquals('secrets!', $model->getAttribute('hiddenField'));
        $this->assertEquals('1234', $model->getAttribute('passwordHash'));
    }

    public function test_model_date_handling()
    {
        $model = new RealModelStub([
            'myField' => '2011-11-11T11:11:11Z',
            'dateField' => '2011-11-11T11:11:11Z',
        ]);

        $this->assertFalse($model->myField instanceof Carbon);
        $this->assertTrue($model->dateField instanceof Carbon);
    }
}
