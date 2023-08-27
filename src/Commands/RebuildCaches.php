<?php

namespace Eloquence\Commands;

use Eloquence\Behaviours\CountCache\CountCache;
use Eloquence\Behaviours\SumCache\SumCache;
use hanneskod\classtools\Iterator\ClassIterator;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Finder\Finder;

class RebuildCaches extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eloquence:rebuild {--class= : Optional classes to update} {--dir= : Directory in which to look for classes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild the caches for one or more Eloquent models.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ($this->classes() as $className) {
            $this->rebuild($className);
        }
    }

    /**
     * Rebuilds the caches for the given class.
     *
     * @param string $className
     */
    private function rebuild(string $className): void
    {
        $model = new $className;

//        if ($model instanceof Countable) {
//            $this->info("Rebuilding [$className] count caches");
//            $countCache = CountCache::for($model);
//            $countCache->rebuild();
//        }
//
//        if ($model instanceof Summable) {
//            $this->info("Rebuilding [$className] sum caches");
//            $sumCache = SumCache::for($model);
//            $sumCache->rebuild();
//        }
    }

    /**
     * @return array
     */
    public function classes(): array
    {
        if ($class = $this->option('class')) {
            $classes = [$class];
        } else {
            $directory = $this->option('dir') ?: app_path();
            $classes = (new FindCacheableClasses($directory))->getAllCacheableClasses();
        }
        return $classes;
    }

}
