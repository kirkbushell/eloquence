<?php
namespace Tests\Stubs;

use Eloquence\Database\Traits\CamelCaseModel;
use Eloquence\Database\Traits\UUIDModel;

class ModelStub extends ParentModelStub
{
    use CamelCaseModel;
    use UUIDModel;
}
