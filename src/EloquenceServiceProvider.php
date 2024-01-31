<?php

namespace Eloquence;

use Eloquence\Utilities\DBQueryLog;
use Illuminate\Support\ServiceProvider;

class EloquenceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/eloquence.php' => config_path('eloquence.php'),
        ], 'config');

        $this->initialiseDbQueryLog();
        $this->initialiseCommands();
    }

    protected function initialiseDbQueryLog(): void
    {
        DBQueryLog::initialise();
    }

    private function initialiseCommands(): void
    {
        $this->commands([
            Utilities\RebuildCaches::class,
        ]);
    }
}
