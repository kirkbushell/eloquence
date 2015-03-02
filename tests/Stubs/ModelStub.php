<?php
namespace Tests\Stubs;

use Eloquence\Database\Traits\CamelCaseModel;

class ModelStub extends ParentModelStub
{
    use CamelCaseModel;
}
