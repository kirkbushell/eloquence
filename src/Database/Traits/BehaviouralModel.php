<?php
namespace Eloquence\Database\Traits;

class BehaviouralModel
{
    /**
     * Define the behaviours that you would like to use within your model.
     *
     *     public $behaviours = [
     *         'Eloquence\Database\Behaviours\CountCache' => 'OtherModel'
     *     ];
     */

    public static function boot()
    {
        parent::boot();

        $events = with(new static)->getObservableEvents();

        foreach ($events as $event) {
            static::$event(function($model) use ($event) {
                static::affect($model, $event);
            });
        }
    }

    public static function affect($event)
    {
        
    }
}
