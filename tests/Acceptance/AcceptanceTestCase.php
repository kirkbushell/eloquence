<?php
namespace Tests\Acceptance;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AcceptanceTestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->migrate();
        $this->init();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', array(
            'driver'   => 'sqlite',
            'database' => ':memory:'
        ));
    }

    protected function init()
    {
        // Overload
    }

    private function migrate()
    {
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('slug')->nullable();
            $table->integer('comment_count')->default(0);
            $table->integer('post_count')->default(0);
            $table->timestamps();
        });

        Schema::create('posts', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('slug')->nullable();
            $table->integer('comment_count')->default(0);
            $table->timestamps();
        });

        Schema::create('comments', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('post_id');
            $table->timestamps();
        });
    }
}
