<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'desc' => $this->faker->text(),
            'price' => $this->faker->numberBetween(25000, 200000),
            'catalog' => $this->faker->randomNumber(1)
        ];
    }
}
