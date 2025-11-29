<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Swis\DateRange\DateRange;
use Workbench\App\Models\StartDateOnlyDefaultNameNullable;

class HasDateRangeStartDateOnlyDefaultNameNullableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_check_active(): void
    {
        $active = StartDateOnlyDefaultNameNullable::factory()->active()->create();
        $future = StartDateOnlyDefaultNameNullable::factory()->future()->create();

        $this->assertTrue($active->isDateRangeActive());
        $this->assertFalse($future->isDateRangeActive());
    }

    #[Test]
    public function it_can_check_past(): void
    {
        $active = StartDateOnlyDefaultNameNullable::factory()->active()->create();
        $future = StartDateOnlyDefaultNameNullable::factory()->future()->create();

        $this->assertFalse($active->isDateRangePast());
        $this->assertFalse($future->isDateRangePast());
    }

    #[Test]
    public function it_can_check_future(): void
    {
        $active = StartDateOnlyDefaultNameNullable::factory()->active()->create();
        $future = StartDateOnlyDefaultNameNullable::factory()->future()->create();

        $this->assertFalse($active->isDateRangeFuture());
        $this->assertTrue($future->isDateRangeFuture());
    }

    #[Test]
    public function it_can_query_active(): void
    {
        StartDateOnlyDefaultNameNullable::factory(4)->active()->create();
        StartDateOnlyDefaultNameNullable::factory(16)->future()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\StartDateOnlyDefaultNameNullable> $active */
        $active = StartDateOnlyDefaultNameNullable::whereDateRangeActive()->get();

        $this->assertCount(4, $active);
        $this->assertTrue($active->every(fn (StartDateOnlyDefaultNameNullable $model) => $model->isDateRangeActive()));
    }

    #[Test]
    public function it_can_query_past(): void
    {
        StartDateOnlyDefaultNameNullable::factory(16)->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\StartDateOnlyDefaultNameNullable> $past */
        $past = StartDateOnlyDefaultNameNullable::whereDateRangePast()->get();

        $this->assertCount(0, $past);
    }

    #[Test]
    public function it_can_query_future(): void
    {
        StartDateOnlyDefaultNameNullable::factory(4)->future()->create();
        StartDateOnlyDefaultNameNullable::factory(16)->active()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\StartDateOnlyDefaultNameNullable> $future */
        $future = StartDateOnlyDefaultNameNullable::whereDateRangeFuture()->get();

        $this->assertCount(4, $future);
        $this->assertTrue($future->every(fn (StartDateOnlyDefaultNameNullable $model) => $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_date_range_overlap(): void
    {
        $dateRange = DateRange::make(now()->subDays(5), now()->addDays(5));

        $overlapEnd = StartDateOnlyDefaultNameNullable::factory()->create(['start_date' => now()]);
        $after = StartDateOnlyDefaultNameNullable::factory()->create(['start_date' => now()->addDays(6)]);
        $wider = StartDateOnlyDefaultNameNullable::factory()->create(['start_date' => now()->subDays(10)]);

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\StartDateOnlyDefaultNameNullable> $models */
        $models = StartDateOnlyDefaultNameNullable::whereDateRangeOverlaps($dateRange)->get();
        $this->assertCount(2, $models);
        $this->assertTrue($models->contains($overlapEnd));
        $this->assertTrue($models->contains($wider));
    }

    #[Test]
    public function it_can_sort_on_active(): void
    {
        StartDateOnlyDefaultNameNullable::factory(5)->active()->create();
        StartDateOnlyDefaultNameNullable::factory(7)->future()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\StartDateOnlyDefaultNameNullable> $models */
        $models = StartDateOnlyDefaultNameNullable::orderByDateRangeActive()->get();

        $this->assertCount(12, $models);

        $this->assertTrue($models->slice(0, 5)->every(fn (StartDateOnlyDefaultNameNullable $model) => $model->isDateRangeActive()));
        $this->assertTrue($models->slice(5)->every(fn (StartDateOnlyDefaultNameNullable $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_gets_end_date(): void
    {
        $model = StartDateOnlyDefaultNameNullable::factory()->create();

        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_accepts_null_end_date(): void
    {
        $model = new StartDateOnlyDefaultNameNullable;
        $model->setEndDate(null);

        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_errors_on_setting_end_date(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $model = new StartDateOnlyDefaultNameNullable;
        $model->setEndDate(now());
    }

    #[Test]
    public function it_accepts_half_open_date_range(): void
    {
        $model = StartDateOnlyDefaultNameNullable::factory()->create();

        $now = now();

        $model->setDateRange(DateRange::make($now, null));

        $this->assertEquals($now->format('Y-m-d'), $model->getStartDate()?->format('Y-m-d'));
        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_errors_on_setting_closed_date_range(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $model = new StartDateOnlyDefaultNameNullable;
        $model->setDateRange(DateRange::make(now(), now()->addDays(5)));
    }

    #[Test]
    public function it_allows_null_dates(): void
    {
        $model = StartDateOnlyDefaultNameNullable::factory()->create(['start_date' => null]);

        $this->assertNull($model->getStartDate());
        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_removes_time_component_from_dates(): void
    {
        $model = StartDateOnlyDefaultNameNullable::factory()->create([
            'start_date' => now()->setTime(10, 10, 10),
        ]);

        $expected = now()->format('Y-m-d');

        $this->assertEquals($expected, $model->start_date?->format('Y-m-d'));
    }
}
