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
    protected $signature = 'eloquence:rebuild {--class= : Optional classes to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild the caches for one or more eloquent models';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        if ($class = $this->option('class')) {
            $classes = [$class];
        } else {
            $classes = $this->getAllCacheableClasses();
        }
        foreach ($classes as $className) {
            $this->rebuild($className);
        }
    }

    /**
     * Iterate through the application's classes and return all the ones
     * that implement one of the caching behaviours.
     *
     * @return array[string]
     */
    private function getAllCacheableClasses()
    {
        $finder = new Finder;
        $iterator = new ClassIterator($finder->in(app_path()));
        $iterator->enableAutoloading();

        $classes = [];

        foreach ($iterator->type(Model::class) as $className => $class) {
            echo "...$className\n";
            if ($class->isInstantiable() && $this->usesCaching($class)) {
                $classes[] = $className;
            }
        }

        return $classes;
    }

    /**
     * Rebuilds the caches for the given class.
     *
     * @param string $className
     */
    private function rebuild($className)
    {
        $instance = new $className;

        if (method_exists($instance, 'countCaches')) {
            $this->info("Rebuilding [$className] count caches");
            $countCache = new CountCache($instance);
            $countCache->rebuild();
        }

        if (method_exists($instance, 'sumCaches')) {
            $this->info("Rebuilding [$className] sum caches");
            $sumCache = new SumCache($instance);
            $sumCache->rebuild();
        }
    }

    /**
     * Decide if the class uses any of the caching behaviours.
     *
     * @param \ReflectionClass $class
     *
     * @return bool
     */
    private function usesCaching(\ReflectionClass $class)
    {
        return $class->hasMethod('bootCountable') || $class->hasMethod('bootSummable');
    }

}
