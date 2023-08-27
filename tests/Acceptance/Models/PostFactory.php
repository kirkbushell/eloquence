<?php

namespace Tests\Acceptance\Models;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;
    
    public function definition()
    {
        return [
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
        ];
    }
}