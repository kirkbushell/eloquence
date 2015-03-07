<?php
namespace Tests\Unit\Stubs;

use Eloquence\Database\Traits\CamelCaseModel;

class ModelStub extends ParentModelStub
{
    use CamelCaseModel;

    protected $attributes = [
        'first_name' => 'Kirk',
        'last_name' => 'Bushell',
        'address' => 'Home',
        'country_of_origin' => 'Australia'
    ];
}
