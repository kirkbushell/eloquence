<?php

namespace Eloquence;

use Illuminate\Support\ServiceProvider;
use Eloquence\Database\Schema\Blueprint;

class EloquenceServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Initialises the service provider, and here we attach our own blueprint
     * resolver to the schema, so as to provide the enhanced functionality.
     */
    public function boot()
    {
        /**
         *  Overload the model class and rebind it to an Eloquence implementation so that we can
         * still make use of certain traits and features that Eloquence provides. Admittedly,
         * this is pretty nasty and is basically a form of monkey-patching. I'm all ears for
         * ideas as to how to do this more cleanly.
         *
         * The main problem is that Eloquent's own relationships end up using the Eloquent model
         * arrangement so it seems impossible for us to inject our own custom implementation without
         * being dirty, dirty coders.
         */
        $this->app->bind('Illuminate\Database\Eloquent\Model', 'Eloquence\Database\Model');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
