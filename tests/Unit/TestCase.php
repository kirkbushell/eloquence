<?php
namespace tests\Unit;

use PHPUnit_Framework_TestCase;
use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->init();
    }

    public function tearDown()
    {
        m::close();
    }

    public function init()
    {
        // Nothing to do - for children to implement.
    }
}
