<?php
namespace Tests\Database\Traits;

use Tests\Stubs\Model;
use Tests\TestCase;

class CamelCaseModelTest extends TestCase
{
	public function init()
    {
        $this->model = new Model;
    }

    public function testSettingOfAttributes()
    {
        $this->model->setAttribute('firstName', 'Kirkus');

        $this->assertEquals('Kirkus', $this->model->attributes['first_name']);
    }

    public function testAttributeRetrieval()
    {
        $this->assertEquals('Kirk', $this->model->getAttribute('firstName'));
    }

    public function testAttributeArrayRetrieval()
    {
        $expectedArray = ['firstName' => 'Kirk', 'lastName' => 'Bushell'];
        $actualArray = $this->model->getAttributes();

        $this->assertEquals($expectedArray, $actualArray);
    }

    public function testNonCamelCaseEnforcement()
    {
        $this->model->enforceCamelCase = false;

        $expectedArray = ['first_name' => 'Kirk', 'last_name' => 'Bushell'];
        $actualArray = $this->model->getAttributes();

        $this->assertEquals('Kirk', $this->model->getAttribute('first_name'));
        $this->assertEquals('Bushell', $this->model->getAttribute('last_name'));
        $this->assertEquals($expectedArray, $actualArray);
    }
}
