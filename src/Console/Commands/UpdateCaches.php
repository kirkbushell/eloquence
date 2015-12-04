<?php
namespace Eloquence\Console\Commands;

use hanneskod\classtools\Iterator\ClassIterator;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class UpdateCaches extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eloquence:update-caches {classes? : Comma-separated list of classes to update}';

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

        $classes = $this->argument('classes');
        if (!$class) {
            $classes = $this->getAllCacheableClasses();
        }

        foreach ($classes as $class) {
            $this->updateCaches($class);
            $this->info("Updated caches for [$class].");
        }
    }

    private function getAllCacheableClasses()
    {
        $finder = new Finder;
        $iterator = new ClassIterator($finder->in('src'));

        foreach ($iterator as $className=>$class) {
            $this->info($className);
        }

    }

    private function updateCaches($class)
    {
        //todo
    }

}
