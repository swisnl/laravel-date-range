<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Swis\DateRange\DateRange;
use Workbench\App\Models\EndDateOnlyDefaultNameNullable;

class HasDateRangeEndDateOnlyDefaultNameNullableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_check_active(): void
    {
        $past = EndDateOnlyDefaultNameNullable::factory()->past()->create();
        $active = EndDateOnlyDefaultNameNullable::factory()->active()->create();

        $this->assertFalse($past->isActive());
        $this->assertTrue($active->isActive());
    }

    #[Test]
    public function it_can_check_past(): void
    {
        $past = EndDateOnlyDefaultNameNullable::factory()->past()->create();
        $active = EndDateOnlyDefaultNameNullable::factory()->active()->create();

        $this->assertTrue($past->isPast());
        $this->assertFalse($active->isPast());
    }

    #[Test]
    public function it_can_check_future(): void
    {
        $past = EndDateOnlyDefaultNameNullable::factory()->past()->create();
        $active = EndDateOnlyDefaultNameNullable::factory()->active()->create();

        $this->assertFalse($past->isFuture());
        $this->assertFalse($active->isFuture());
    }

    #[Test]
    public function it_can_query_active(): void
    {
        EndDateOnlyDefaultNameNullable::factory(1)->past()->create();
        EndDateOnlyDefaultNameNullable::factory(4)->active()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\EndDateOnlyDefaultNameNullable> $active */
        $active = EndDateOnlyDefaultNameNullable::whereActive()->get();

        $this->assertCount(4, $active);
        $this->assertTrue($active->every(fn (EndDateOnlyDefaultNameNullable $model) => $model->isActive()));
    }

    #[Test]
    public function it_can_query_past(): void
    {
        EndDateOnlyDefaultNameNullable::factory(1)->active()->create();
        EndDateOnlyDefaultNameNullable::factory(4)->past()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\EndDateOnlyDefaultNameNullable> $past */
        $past = EndDateOnlyDefaultNameNullable::wherePast()->get();

        $this->assertCount(4, $past);
        $this->assertTrue($past->every(fn (EndDateOnlyDefaultNameNullable $model) => $model->isPast()));
    }

    #[Test]
    public function it_can_query_future(): void
    {
        EndDateOnlyDefaultNameNullable::factory(1)->past()->create();
        EndDateOnlyDefaultNameNullable::factory(16)->active()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\EndDateOnlyDefaultNameNullable> $future */
        $future = EndDateOnlyDefaultNameNullable::whereFuture()->get();

        $this->assertCount(0, $future);
    }

    #[Test]
    public function it_can_query_date_range_overlap(): void
    {
        $dateRange = DateRange::make(now()->subDays(5), now()->addDays(5));

        $before = EndDateOnlyDefaultNameNullable::factory()->create(['end_date' => now()->subDays(6)]);
        $overlapStart = EndDateOnlyDefaultNameNullable::factory()->create(['end_date' => now()]);
        $wider = EndDateOnlyDefaultNameNullable::factory()->create(['end_date' => now()->addDays(10)]);

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\EndDateOnlyDefaultNameNullable> $models */
        $models = EndDateOnlyDefaultNameNullable::whereDateRangeOverlaps($dateRange)->get();
        $this->assertCount(2, $models);
        $this->assertTrue($models->contains($overlapStart));
        $this->assertTrue($models->contains($wider));
    }

    #[Test]
    public function it_can_sort_on_active(): void
    {
        EndDateOnlyDefaultNameNullable::factory(3)->past()->create();
        EndDateOnlyDefaultNameNullable::factory(5)->active()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\EndDateOnlyDefaultNameNullable> $models */
        $models = EndDateOnlyDefaultNameNullable::query()->orderByActive()->get();

        $this->assertCount(8, $models);

        $this->assertTrue($models->slice(0, 5)->every(fn (EndDateOnlyDefaultNameNullable $model) => $model->isActive()));
        $this->assertTrue($models->slice(5)->every(fn (EndDateOnlyDefaultNameNullable $model) => $model->isPast() || $model->isFuture()));
    }

    #[Test]
    public function it_gets_start_date(): void
    {
        $model = EndDateOnlyDefaultNameNullable::factory()->create();

        $this->assertNull($model->getStartDate());
    }

    #[Test]
    public function it_accepts_null_start_date(): void
    {
        $model = new EndDateOnlyDefaultNameNullable;
        $model->setStartDate(null);

        $this->assertNull($model->getStartDate());
    }

    #[Test]
    public function it_errors_on_setting_start_date(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $model = new EndDateOnlyDefaultNameNullable;
        $model->setStartDate(now());
    }

    #[Test]
    public function it_accepts_half_open_date_range(): void
    {
        $model = EndDateOnlyDefaultNameNullable::factory()->create();

        $now = now();

        $model->setDateRange(DateRange::make(null, $now));

        $this->assertNull($model->getStartDate());
        $this->assertEquals($now->format('Y-m-d'), $model->getEndDate()?->format('Y-m-d'));
    }

    #[Test]
    public function it_errors_on_setting_closed_date_range(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $model = new EndDateOnlyDefaultNameNullable;
        $model->setDateRange(DateRange::make(now(), now()->addDays(5)));
    }

    #[Test]
    public function it_allows_null_dates(): void
    {
        $model = EndDateOnlyDefaultNameNullable::factory()->create(['end_date' => null]);

        $this->assertNull($model->getStartDate());
        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_removes_time_component_from_dates(): void
    {
        $model = EndDateOnlyDefaultNameNullable::factory()->create([
            'end_date' => now()->setTime(20, 20, 20),
        ]);

        $expected = now()->format('Y-m-d');

        $this->assertEquals($expected, $model->end_date?->format('Y-m-d'));
    }
}
