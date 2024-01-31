<?php

namespace Eloquence\Utilities;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DBQueryLog
{
    /**
     * Enables the db query log. The default logging driver is set to the your logging default. However, if you wish
     * to configure this to use a different driver specifically for query logs (recommended), then you can configure
     * that in the eloquence configuration file.
     *
     * @return void
     */
    public static function initialise(): void
    {
        if (!config('eloquence.logging.enabled')) {
            return;
        }

        DB::listen(function (QueryExecuted $query) {
            Log::driver(config('eloquence.logging.driver'))->debug("[{$query->time}ms] $query->sql", $query->bindings);
        });
    }
}
