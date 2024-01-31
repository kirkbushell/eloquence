<?php
namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\HasCamelCasing;

class PivotModelStub extends ParentModelStub
{
    use \Eloquence\Behaviours\HasCamelCasing;

    protected $attributes = [
        'first_name' => 'Kirk',
        'pivot_field' => 'whatever'
    ];
}
