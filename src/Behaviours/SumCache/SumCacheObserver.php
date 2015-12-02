<?php
namespace Eloquence\Behaviours\SumCache;

use Illuminate\Database\Eloquent\Model;

class SumCacheObserver
{
    /**
     * Stores the cache manager which handles the cache strategies.
     *
     * @var SumCacheManager
     */
    private $manager;

    /**
     * Whenever a new model is created, we want to increase the related model's sum cache.
     *
     * @param Model $model
     */
    public function created(Model $model)
    {
        $this->manager()->increase($model);
    }

    /**
     * When a model is updated, update the entire cache. The reason for this is because a related model
     * value may have changed, so we may have to increase one related object, and decrease another.
     *
     * @param Model $model
     */
    public function updated(Model $model)
    {
        $this->manager()->updateCache($model);
    }

    /**
     * Whenever a model is deleted, we decrease the related model object's sum cache.
     *
     * @param Model $model
     */
    public function deleted(Model $model)
    {
        $this->manager()->decrease($model);
    }

    /**
     * When a model is restored (aka, undeleted) we need to again increase the related
     * model objects' sum caches.
     *
     * @param Model $model
     */
    public function restored(Model $model)
    {
        $this->manager()->increase($model);
    }

    /**
     * Manages the sum cache manager instance.
     *
     * @return SumCacheManager
     */
    protected function manager()
    {
        if (! $this->manager) {
            $this->manager = new SumCacheManager;
        }

        return $this->manager;
    }

    /**
     * @param SumCacheManager $manager
     */
    public function setManager(SumCacheManager $manager)
    {
        $this->manager = $manager;
    }
}
