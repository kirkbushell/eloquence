<?php

namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasCamelCasing;
    use HasFactory;

    protected $fillable = [
        'post_count'
    ];

    protected static function newFactory(): Factory
    {
        return CategoryFactory::new();
    }
}
