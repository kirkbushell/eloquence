<?php
namespace Tests\Unit\Stubs;

use Eloquence\Database\Traits\CamelCaseModel;

class PivotModelStub extends ParentModelStub
{
	use CamelCaseModel;

    protected $attributes = [
        'first_name' => 'Kirk',
        'pivot_field' => 'whatever'
    ];
}
