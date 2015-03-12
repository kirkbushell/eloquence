<?php
namespace Eloquence\Behaviours\Slugged;

use Hashids\Hashids;
use Illuminate\Support\Str;

class Slug
{
    /**
     * @var string
     */
    private $slug;

    /**
     * Creates a new instance of the Slug class based on the slug string provided.
     *
     * @param string $slug
     */
    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Generate a new 8-character slug.
     *
     * @param integer $id
     * @return Slug
     */
    public static function fromId($id)
    {
        $salt = md5(uniqid().$id);
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $slug = with(new Hashids($salt, $length = 8, $alphabet))->encode($id);

        return new Slug($slug);
    }

    /**
     * Creates a new slug from a string title.
     *
     * @param string $title
     * @return Slug
     */
    public static function fromTitle($title)
    {
        return new Slug(Str::slug($title));
    }

    /**
     * Returns a string value for the Slug.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->slug;
    }
}
