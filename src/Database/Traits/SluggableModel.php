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

        $fields = array_map(function($field) {
            if (str_contains($field, '.')) {
                return object_get($this, $field); // this acts as a delimiter, which we can replace with /
            }
            else {
                return $this->{$field};
            }
        }, $fields);

        $titleSlug = Slug::fromTitle(implode('-', $fields));

        // This is not the first time we've attempted to create a title slug, so - let's make it more unique
        if ($attempts > 0) {
            $titleSlug . "-{$attempts}";
        }

        $this->setSlugValue($titleSlug);
        
        $attempts++;
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

    /**
     * Unfortunately we need to overload the save method. The reason for this is simple - Eloquence
     * does not require you to configure your model for slugging uniqueness - it lets the database
     * tell it if something goes awry during slug creation. If it does, it'll attempt again with
     * a fresh set of unique slug values.
     *
     * @param array $options
     * @return mixed
     * @throws \Exception
     */
    public function save(array $options = [])
    {
        try {
            return parent::save($options);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate') && str_contains($e->getMessage(), $this->slugField)) {
                $this->generateSlug();

                return $this->save($options);
            }
            else {
                throw $e;
            }
        }
    }
}
