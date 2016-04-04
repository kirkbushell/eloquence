<?php
namespace Eloquence\Behaviours;

use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * Class Uuid
 *
 * Manages the usage of creating UUID values for primary keys. Drop into your models as
 * per normal to use this functionality. Works right out of the box.
 *
 * Taken from: http://garrettstjohn.com/entry/using-uuids-laravel-eloquent-orm/
 *
 * @package Eloquence\Database\Traits
 */
trait Uuid
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function bootUuid()
    {
        /**
         * Attach to the 'creating' Model Event to provide a UUID
         * for the `id` field (provided by $model->getKeyName())
         */
        static::creating(function ($model) {
            $key = $model->getKeyName();

            if (empty($model->$key)) {
                $model->$key = (string) $model->generateNewUuid();
            }
        });
    }

    /**
     * Get a new version 4 (random) UUID.
     *
     * @return \Rhumsaa\Uuid\Uuid
     */
    public function generateNewUuid()
    {
        return RamseyUuid::uuid4();
    }
}
