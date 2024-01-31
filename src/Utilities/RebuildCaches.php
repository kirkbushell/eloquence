<?php

namespace Eloquence\Utilities;

use Eloquence\Behaviours\CountCache\HasCounts;
use Eloquence\Behaviours\SumCache\HasSums;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class RebuildCaches extends Command
{
    protected $signature = 'eloquence:rebuild-caches {path? : The absolute path to search for models. Defaults to your application path.}';
    protected $description = 'Rebuilds the caches of all affected models within the database.';

    private $caches = [
        HasCounts::class => 'rebuildCountCache',
        HasSums::class => 'rebuildSumCache',
    ];

    public function handle(): void
    {
        $path = $this->argument('path') ?? app_path();

        $this->allModelsUsingCaches($path)->each(function (string $class) {
            $traits = class_uses_recursive($class);

            foreach ($this->caches as $trait => $method) {
                if (!in_array($trait, $traits)) {
                    continue;
                }

                $class::$method();
            }
        });
    }

    /**
     * Returns only those models that are utilising eloquence cache mechanisms.
     *
     * @param string $path
     * @return Collection
     */
    private function allModelsUsingCaches(string $path): Collection
    {
        return collect(Finder::create()->files()->in($path)->name('*.php'))
            ->filter(fn (SplFileInfo $file) => $file->getFilename()[0] === Str::upper($file->getFilename()[0]))
            ->map(fn (SplFileInfo $file) => $this->fullyQualifiedClassName($file))
            ->filter(fn (string $class) => is_subclass_of($class, Model::class))
            ->filter(fn (string $class) => $this->usesCaches($class));
    }

    /**
     * Determines the fully qualified class name of the provided file.
     *
     * @param SplFileInfo $file
     * @return string
     */
    private function fullyQualifiedClassName(SplFileInfo $file)
    {
        $tokens = \PhpToken::tokenize($file->getContents());
        $namespace = null;
        $class = null;

        foreach ($tokens as $i => $token) {
            if ($token->is(T_NAMESPACE)) {
                $namespace = $tokens[$i + 2]->text;
            }

            if ($token->is(T_CLASS)) {
                $class = $tokens[$i + 2]->text;
            }

            if ($namespace && $class) {
                break;
            }
        }

        if (!$namespace || !$class) {
            $this->error(sprintf('Could not find namespace or class in %s', $file->getRealPath()));
        }

        return sprintf('%s\\%s', $namespace, $class);
    }

    /**
     * Returns true if the provided class uses any of the caches provided by Eloquence.
     *
     * @param string $class
     * @return bool
     */
    private function usesCaches(string $class): bool
    {
        return (bool) array_intersect(class_uses_recursive($class), array_keys($this->caches));
    }
}
