<?php

namespace Tests\Acceptance\Models;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;
    
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => fake()->numberBetween(0, 100),
        ];
    }
}