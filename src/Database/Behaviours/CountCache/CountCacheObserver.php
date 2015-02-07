<?php
namespace Eloquence\Database\Behaviours\CountCache;

use Illuminate\Database\Eloquent\Model;

class CountCacheObserver
{
    /**
     * Stores the cache manager which handles the cache strategies.
     *
     * @var CountCacheManager
     */
    private $manager;

    /**
     * @param CountCacheManager $manager
     */
    public function __construct(CountCacheManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Whenever a new model is created, we want to increment the related model's count cache.
     *
     * @param Model $model
     */
    public function created(Model $model)
    {
        $this->manager->increment($model);
    }

    /**
     * Store the original data before the model is updated. When a model is updated the original
     * array is set to the newly updated data, so it's impossible to figure out whether a related
     * column value has changed.
     *
     * @param Model $model
     */
    public function updating(Model $model)
    {
        $this->manager->setOriginal($model->getOriginal());
    }

    /**
     * When a model is updated, update the entire cache. The reason for this is because a related model
     * value may have changed, so we may have to increment one related object, and decrement another.
     *
     * @param Model $model
     */
    public function updated(Model $model)
    {
        $this->manager->updateCache($model);
    }

    /**
     * Whenever a model is deleted, we decrement the related model object's count cache.
     *
     * @param Model $model
     */
    public function deleted(Model $model)
    {
        $this->manager->decrement($model);
    }

    /**
     * When a model is restored (aka, undeleted) we need to again increment the related
     * model objects' count caches.
     *
     * @param Model $model
     */
    public function restored(Model $model)
    {
        $this->manager->increment($model);
    }
}
