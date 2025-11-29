<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\Database\Factories\Concerns\GeneratesDates;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Workbench\App\Models\BothDefaultNameStartDateRequired>
 */
class BothDefaultNameStartDateRequiredFactory extends Factory
{
    use GeneratesDates;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        $startDate = $this->generateDateBetween('-1 year', '+1 year');
        $endDate = fake()->boolean() ? $this->generateDateBetween($startDate, $startDate->addYear()) : null;

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->generateDateBetween('-2 years', '-1 year');
            $endDate = $this->generateDateBetween($startDate, '-1 month');

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->generateDateBetween('-1 year', 'now');
            $endDate = fake()->boolean() ? $this->generateDateBetween('now', '+1 year') : null;

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    public function future(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->generateDateBetween('+1 month', '+1 year');
            $endDate = fake()->boolean() ? $this->generateDateBetween($startDate, '+2 years') : null;

            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    public function yesterday(): static
    {
        return $this->state(function (array $attributes) {
            $date = now()->subDay()->startOfDay();

            return [
                'start_date' => $date,
                'end_date' => $date,
            ];
        });
    }

    public function today(): static
    {
        return $this->state(function (array $attributes) {
            $date = now()->startOfDay();

            return [
                'start_date' => $date,
                'end_date' => $date,
            ];
        });
    }

    public function tomorrow(): static
    {
        return $this->state(function (array $attributes) {
            $date = now()->addDay()->startOfDay();

            return [
                'start_date' => $date,
                'end_date' => $date,
            ];
        });
    }
}
