<?php
namespace Tests\Unit\Stubs;

use Eloquence\Database\Traits\CamelCasing;

class PivotModelStub extends ParentModelStub
{
	use CamelCasing;

    protected $attributes = [
        'first_name' => 'Kirk',
        'pivot_field' => 'whatever'
    ];
}
