<?php
namespace Tests\Unit\Stubs;

use Eloquence\Behaviours\CamelCased;

class PivotModelStub extends ParentModelStub
{
    use \Eloquence\Behaviours\CamelCased;

    protected $attributes = [
        'first_name' => 'Kirk',
        'pivot_field' => 'whatever'
    ];
}
