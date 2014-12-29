<?php
namespace Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
	public function setUp()
    {
        parent::setUp();

        $this->init();
    }

    public function init()
    {
        // Nothing to do - for children to implement.
    }
}
