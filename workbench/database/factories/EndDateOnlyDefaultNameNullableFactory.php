<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\Database\Factories\Concerns\GeneratesDates;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Workbench\App\Models\EndDateOnlyDefaultNameNullable>
 */
class EndDateOnlyDefaultNameNullableFactory extends Factory
{
    use GeneratesDates;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        $endDate = fake()->boolean() ? $this->generateDateBetween('-1 year', '+2 years') : null;

        return [
            'end_date' => $endDate,
        ];
    }

    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $endDate = $this->generateDateBetween('-2 years', '-1 month');

            return [
                'end_date' => $endDate,
            ];
        });
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $endDate = fake()->boolean() ? $this->generateDateBetween('now', '+1 year') : null;

            return [
                'end_date' => $endDate,
            ];
        });
    }
}
