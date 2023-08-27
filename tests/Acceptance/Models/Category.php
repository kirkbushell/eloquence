<?php

namespace Tests\Acceptance\Models;

use Eloquence\Behaviours\CamelCased;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use CamelCased;
    use HasFactory;

    protected static function newFactory(): Factory
    {
        return CategoryFactory::new();
    }
}
