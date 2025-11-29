<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\Database\Factories\Concerns\GeneratesDates;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Workbench\App\Models\StartDateOnlyDefaultNameNullable>
 */
class StartDateOnlyDefaultNameNullableFactory extends Factory
{
    use GeneratesDates;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        $startDate = fake()->boolean() ? $this->generateDateBetween('-1 year', '+1 year') : null;

        return [
            'start_date' => $startDate,
        ];
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->boolean() ? $this->generateDateBetween('-1 year', 'now') : null;

            return [
                'start_date' => $startDate,
            ];
        });
    }

    public function future(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->generateDateBetween('+1 month', '+1 year');

            return [
                'start_date' => $startDate,
            ];
        });
    }
}
