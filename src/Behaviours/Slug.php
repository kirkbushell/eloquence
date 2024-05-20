<?php

namespace Eloquence\Behaviours;

use Hashids\Hashids;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;

class Slug implements Castable, Jsonable
{
    /**
     * @var string
     */
    private $slug;

    /**
     * Creates a new instance of the Slug class based on the slug string provided.
     *
     * @param string $slug
     * @throws \Exception
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
        $slug = with(new Hashids($salt, 8, $alphabet))->encode($id);

        return new Slug($slug);
    }

    /**
     * Generate a new entirely random 8-character slug
     */
    public static function random(): Slug
    {
        $exclude = ['/', '+', '=', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $length = 8;
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace($exclude, '', base64_encode($bytes)), 0, $size);
        }

        return new Slug($string);
    }

    public static function fromTitle($title): Slug
    {
        return new Slug(Str::slug($title));
    }

    public function __toString(): string
    {
        return $this->slug;
    }

    public function toJson($options = 0): string
    {
        return $this->__toString();
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get($model, string $key, $value, array $attributes): ?Slug
            {
                return null === $value ? $value : new Slug($value);
            }

            public function set($model, string $key, $value, array $attributes): array
            {
                return [
                    $key => null === $value ? $value : (string) $value
                ];
            }
        };
    }
}