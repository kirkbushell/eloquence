<?php
namespace Eloquence\Database\Traits;

use Eloquence\Behaviours\Slugged\Slug;

trait SluggableModel
{
    /**
     * Generate a slug based on the main model key.
     */
    public function generateIdSlug()
    {
        $this->setSlugValue(Slug::fromId($this->getKey()));
    }

    /**
     * Generate a slug string based on the fields required.
     */
    public function generateTitleSlug(array $fields)
    {
        $fields = array_map(function($field) {
            return $this->$field;
        }, $fields);

        $this->setSlugValue(Slug::fromTitle(implode(' ', $fields)));
    }

    /**
     * Set the value of the slug.
     *
     * @param $value
     */
    public function setSlugValue(Slug $value)
    {
        $this->{$this->slugField()} = (string) $value;
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
    public function slugStrategy()
    {
        return 'id';
    }
}
