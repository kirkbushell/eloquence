<?php
namespace Tests\Behaviours\CountCache;

use Eloquence\Behaviours\CountCache\CountCacheManager;
use Tests\Stubs\RealModelStub;
use Tests\TestCase;

class CountCacheManagerTest extends TestCase
{
    public function init()
    {
        $this->manager = new CountCacheManager;
    }

    public function testGetTable()
    {
        $this->assertEquals('real_model_stubs', $this->manager->getTable(new RealModelStub));
        $this->assertEquals('real_model_stubs', $this->manager->getTable('Tests\Stubs\RealModelStub'));
    }
}
