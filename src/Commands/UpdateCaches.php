<?php
namespace Eloquence\Commands;

use hanneskod\classtools\Iterator\ClassIterator;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Finder\Finder;

class UpdateCaches extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eloquence:update-caches {--class= : Optional classes to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the caches for one or more eloquent models';

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
        foreach ($classes as $class) {
            $this->updateCaches($class);
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
            if ($class->isInstantiable() && $this->usesCaching($class)) {
                $classes[] = $className;
            }
        }

        return $classes;
    }

    private function updateCaches($class)
    {
        $instance = app($class);

        if (method_exists($instance, 'rebuildCountCaches')) {
            $instance->rebuildCountCaches();
            $this->info("Rebuilt count caches for {$class}.");
        }
        if (method_exists($instance, 'rebuildSumCaches')) {
            $instance->rebuildSumCaches();
            $this->info("Rebuilt sum caches for {$class}.");
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

        return $class->hasMethod('rebuildCacheRecord');
    }

}
