<?php
namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\CamelCasing;

class PivotModelStub extends ParentModelStub
{
    use \Eloquence\Behaviours\CamelCasing;

    protected $attributes = [
        'first_name' => 'Kirk',
        'pivot_field' => 'whatever'
    ];
}
