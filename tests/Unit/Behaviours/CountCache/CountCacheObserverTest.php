<?php
namespace Tests\Unit\Behaviours\CountCache;

use Eloquence\Behaviours\CountCache\CountCacheObserver;
use Mockery as m;
use Tests\Unit\Stubs\RealModelStub;
use Tests\Unit\TestCase;

class CountCacheObserverTest extends TestCase
{
    private $model;
    private $mockManager;
    private $observer;

    public function init()
    {
        $this->mockManager = m::spy('Eloquence\Behaviours\CountCache\CountCacheManager');
        $this->observer = new CountCacheObserver;
        $this->observer->setManager($this->mockManager);
        $this->model = new RealModelStub;
    }

    public function testCreated()
    {
        $this->observer->created($this->model);
        $this->mockManager->shouldHaveReceived('increment')->with($this->model)->once();
    }

    public function testUpdated()
    {
        $this->observer->updated($this->model);
        $this->mockManager->shouldHaveReceived('updateCache')->with($this->model)->once();
    }

    public function testDeleted()
    {
        $this->observer->deleted($this->model);
        $this->mockManager->shouldHaveReceived('decrement')->with($this->model)->once();
    }

    public function testRestored()
    {
        $this->observer->restored($this->model);
        $this->mockManager->shouldHaveReceived('increment')->with($this->model)->once();
    }
}
