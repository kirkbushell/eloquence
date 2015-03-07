<?php
namespace Eloquence\Database\Traits;

use Eloquence\Behaviours\Slugged\Slug;

trait SluggableModel
{
    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            $strategy = static::strategy();

            if ($strategy == 'uuid') {
                $model->generateIdSlug();
            }
            elseif ($strategy != 'id') {
                $model->generateTitleSlug($strategy);
            }
        });

        static::created(function($model) {
            if (static::strategy() == 'id') {
                $model->generateIdSlug();
                $model->save();
            }
        });
    }

    /**
     * Generate a slug based on the main model key.
     */
    public function generateIdSlug()
    {
        $this->{$this->slugField()} = Slug::fromId($this->getKey());
    }

    /**
     * Generate a slug string based on the fields required.
     */
    public function generateTitleSlug($fields)
    {
        array_map(function($field) { return $this->$field; }, explode('.', $fields));

        $this->{$this->slugField()} = Slug::fromTitle(implode(' ', $fields));
    }

    /**
     * Allows laravel to start using the sluggable field as the string for routes.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        $slug = $this->slugField();

        return $this->$slug;
    }

    /**
     * Return the name of the field you wish to use for the slug.
     *
     * @return string
     */
    protected function slugField()
    {
        return 'slug';
    }

    /**
     * Return the strategy to use for the slug.
     *
     * When using id or uuid, simply return 'id' or 'uuid' from the method below. However,
     * for creating a title-based slug - simply return the field you want it to be based on
     *
     * Eg:
     *
     *  return 'id';
     *  return 'uuid';
     *  return 'name';
     *
     * If you'd like your slug to be based on more than one field, return it in dot-notation:
     *
     *  return 'first_name.last_name';
     *
     * If you're using the camelcase model trait, then you can use that format:
     *
     *  return 'firstName.lastName';
     *
     * @return string
     */
    abstract protected static function strategy();
}
