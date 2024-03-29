<?php
namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\HasCamelCasing;

class ModelStub extends ParentModelStub
{
    use \Eloquence\Behaviours\HasCamelCasing;

    protected $attributes = [
        'first_name' => 'Kirk',
        'last_name' => 'Bushell',
        'address' => 'Home',
        'country_of_origin' => 'Australia'
    ];
}
