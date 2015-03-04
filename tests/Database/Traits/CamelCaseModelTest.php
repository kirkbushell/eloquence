<?php
namespace Tests\Database\Traits;

use Tests\Stubs\ModelStub;
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

    public function testCreatingModelByUsingCreate()
    {
        $attributes = [
            'address' => 'Home',
            'countryOfOrigin' => 'Australia',
            'firstName' => 'Kirk',
            'lastName' => 'Bushell'
        ];

        $expectedAttributes = [
            'address' => 'Home',
            'country_of_origin' => 'Australia',
            'first_name' => 'Kirk',
            'last_name' => 'Bushell'
        ];

        $model = ModelStub::create($attributes); //Create local instance of ModelStub since we are testing an alternative method of creating a model.

        $this->assertEquals($expectedAttributes, $model->rawAttributesToArray());

    }
}
