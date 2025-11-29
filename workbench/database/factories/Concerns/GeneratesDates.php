<?php

namespace Workbench\Database\Factories\Concerns;

use Carbon\CarbonImmutable;
use DateTimeInterface;

trait GeneratesDates
{
    public function generateDateBetween(DateTimeInterface|string $startDate = '-30 years', DateTimeInterface|string $endDate = 'now'): CarbonImmutable
    {
        $startDate = CarbonImmutable::parse($startDate)->startOfDay();
        $endDate = CarbonImmutable::parse($endDate)->endOfDay();

        $days = intval($startDate->diffInDays($endDate));

        $randomDays = fake()->numberBetween(0, $days);

        return $startDate->addDays($randomDays);
    }
}
