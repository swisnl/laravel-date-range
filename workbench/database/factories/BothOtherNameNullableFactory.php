<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\Database\Factories\Concerns\GeneratesDates;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Workbench\App\Models\BothOtherNameNullable>
 */
class BothOtherNameNullableFactory extends Factory
{
    use GeneratesDates;

    /**
     * {@inheritDoc}
     */
    public function definition(): array
    {
        $startDate = fake()->boolean() ? $this->generateDateBetween('-1 year', '+1 year') : null;
        $endDate = fake()->boolean() ? $this->generateDateBetween($startDate ?: '-1 year', $startDate ? $startDate->addYear() : '+2 years') : null;

        return [
            'foo' => $startDate,
            'bar' => $endDate,
        ];
    }

    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->boolean() ? $this->generateDateBetween('-2 years', '-1 year') : null;
            $endDate = $this->generateDateBetween($startDate ?: '-2 years', '-1 month');

            return [
                'foo' => $startDate,
                'bar' => $endDate,
            ];
        });
    }

    public function active(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = fake()->boolean() ? $this->generateDateBetween('-1 year', 'now') : null;
            $endDate = fake()->boolean() ? $this->generateDateBetween('now', '+1 year') : null;

            return [
                'foo' => $startDate,
                'bar' => $endDate,
            ];
        });
    }

    public function future(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->generateDateBetween('+1 month', '+1 year');
            $endDate = fake()->boolean() ? $this->generateDateBetween($startDate, '+2 years') : null;

            return [
                'foo' => $startDate,
                'bar' => $endDate,
            ];
        });
    }

    public function yesterday(): static
    {
        return $this->state(function (array $attributes) {
            $date = now()->subDay()->startOfDay();

            return [
                'foo' => $date,
                'bar' => $date,
            ];
        });
    }

    public function today(): static
    {
        return $this->state(function (array $attributes) {
            $date = now()->startOfDay();

            return [
                'foo' => $date,
                'bar' => $date,
            ];
        });
    }

    public function tomorrow(): static
    {
        return $this->state(function (array $attributes) {
            $date = now()->addDay()->startOfDay();

            return [
                'foo' => $date,
                'bar' => $date,
            ];
        });
    }
}
