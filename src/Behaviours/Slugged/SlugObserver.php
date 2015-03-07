<?php
namespace Eloquence\Behaviours\Slugged;

use Illuminate\Database\Eloquent\Model;

class SlugObserver
{
    /**
     * When a new model is created, if we're working with titles or uuids
     * then we can set the slug before it is saved.
     *
     * @param $model
     */
    public function creating(Model $model)
    {
        $strategy = $model->slugStrategy();

        if ($strategy == 'uuid') {
            $model->generateIdSlug();
        }
        elseif ($strategy != 'id') {
            $model->generateTitleSlug((array) $strategy);
        }
    }

    /**
     * However, with normal autoincrement ids - we have to set the slug after
     * the model has been persisted to the database - as then we have the id.
     *
     * @param $model
     */
    public function created(Model $model)
    {
        if ($model->slugStrategy() == 'id') {
            $model->generateIdSlug();
            $model->save();
        }
    }
}
