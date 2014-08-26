<?php

namespace Eloquence\Database\Traits;

/**
 * Class UUIDModel
 *
 * Manages the usage of creating UUID values for primary keys. Drop into your models as
 * per normal to use this functionality. Works rightout of the box.
 *
 * Taken from: http://garrettstjohn.com/entry/using-uuids-laravel-eloquent-orm/
 *
 * @package Eloquence\Database\Traits
 */

trait UUIDModel
{
	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();

		/**
		 * Attach to the 'creating' Model Event to provide a UUID
		 * for the `id` field (provided by $model->getKeyName())
		 */
		self::creating(function ($model) {
			$model->{$model->getKeyName()} = (string)$model->generateNewId();
		});
	}

	/**
	 * Get a new version 4 (random) UUID.
	 *
	 * @return \Rhumsaa\Uuid\Uuid
	 */
	public function generateNewId()
	{
		return Uuid::uuid4();
	}
} 