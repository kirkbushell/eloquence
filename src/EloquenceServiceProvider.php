<?php
namespace Eloquence;

use Eloquence\Utilities\DBQueryLog;
use Illuminate\Support\ServiceProvider;

class EloquenceServiceProvider extends ServiceProvider
{
    /**
     * Initialises the service provider, and here we attach our own blueprint
     * resolver to the schema, so as to provide the enhanced functionality.
     */
    public function boot()
    {
        DBQueryLog::initialise();
    }
}
