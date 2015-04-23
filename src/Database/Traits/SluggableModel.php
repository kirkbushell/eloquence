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
        static $attempts = 0;

        $titleSlug = Slug::fromTitle(implode('-', $this->getTitleFields($fields)));

        // This is not the first time we've attempted to create a title slug, so - let's make it more unique
        if ($attempts > 0) {
            $titleSlug . "-{$attempts}";
        }

        $this->setSlugValue($titleSlug);

        $attempts++;
    }

    /**
     * Because a title slug can be created from multiple sources (such as an article title, a category title.etc.),
     * this allows us to search out those fields from related objects and return the combined values.
     *
     * @param array $fields
     * @return array
     */
    public function getTitleFields(array $fields)
    {
        $fields = array_map(function($field) {
            if (str_contains($field, '.')) {
                return object_get($this, $field); // this acts as a delimiter, which we can replace with /
            }
            else {
                return $this->{$field};
            }
        }, $fields);

        return $fields;
    }

    /**
     * Generate the slug for the model based on the model's slug strategy.
     */
    public function generateSlug()
    {
        $strategy = $this->slugStrategy();

        if ($strategy == 'uuid') {
            $this->generateIdSlug();
        }
        elseif ($strategy != 'id') {
            $this->generateTitleSlug((array) $strategy);
        }
    }

    /**
     * Set the value of the slug.
     *
     * @param $value
     */
    public function setSlugValue(Slug $value)
    {
        $this->{$this->slugField()} = $value;
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

    /**
     * Sets the slug attribute with the Slug value object.
     *
     * @param Slug $slug
     */
    public function setSlugAttribute(Slug $slug)
    {
        $this->attributes[$this->slugField()] = (string) $slug;
    }

    /**
     * Returns the slug attribute as a Slug value object.
     *
     * @return Slug
     */
    public function getSlugAttribute()
    {
        return new Slug($this->attributes[$this->slugField()]);
    }
}
