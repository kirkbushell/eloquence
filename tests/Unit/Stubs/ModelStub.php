<?php
namespace Tests\Unit\Stubs;

use Eloquence\Database\Traits\CamelCasing;

class ModelStub extends ParentModelStub
{
    use CamelCasing;

    protected $attributes = [
        'first_name' => 'Kirk',
        'last_name' => 'Bushell',
        'address' => 'Home',
        'country_of_origin' => 'Australia'
    ];
}
