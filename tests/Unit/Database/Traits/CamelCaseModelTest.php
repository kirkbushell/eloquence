<?php
namespace Tests\Unit\Database\Traits;

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
        $model = new RealModelStub([
            'myField' => 'value',
            'anotherField' => 'yeah',
            'someField' => 'whatever'
        ]);

        $this->assertEquals($model->myField, 'value');
        $this->assertEquals($model->anotherField, 'yeah');
        $this->assertNull($model->someField);
    }

    public function testRelationalMethods()
    {
        $this->setExpectedException('LogicException');

        $model = new RealModelStub;
        $model->fakeRelationship;
    }

    public function testModelHidesHiddenFields()
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

    public function testModelExposesHiddenFields()
    {
        $model = new RealModelStub([
            'myField' => 'value',
            'anotherField' => 'yeah',
            'someField' => 'whatever',
            'hiddenField' => 'secrets!',
            'passwordHash' => '1234',
        ]);

        $hidden = $model->withHidden(['hiddenField', 'passwordHash'])->toArray();

        $this->assertTrue(isset($hidden['hiddenField']));
        $this->assertTrue(isset($hidden['passwordHash']));
        
        $this->assertEquals('secrets!', $hidden['hiddenField']);
        $this->assertEquals('1234', $hidden['passwordHash']);
    }

    public function testModelDateFieldHandling()
    {
        $model = new RealModelStub([
            'myField' => '2011-11-11T11:11:11Z',
            'dateField' => '2011-11-11T11:11:11Z',
        ]);

        $this->assertFalse($model->myField instanceof Carbon);
        $this->assertTrue($model->dateField instanceof Carbon);
    }
}
