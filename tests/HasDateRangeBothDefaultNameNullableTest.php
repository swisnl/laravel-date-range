<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Swis\DateRange\DateRange;
use Swis\DateRange\DateRangeSet;
use Workbench\App\Models\BothDefaultNameNullable;

class HasDateRangeBothDefaultNameNullableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_check_active(): void
    {
        $past = BothDefaultNameNullable::factory()->past()->create();
        $yesterday = BothDefaultNameNullable::factory()->yesterday()->create();
        $active = BothDefaultNameNullable::factory()->active()->create();
        $today = BothDefaultNameNullable::factory()->today()->create();
        $future = BothDefaultNameNullable::factory()->future()->create();
        $tomorrow = BothDefaultNameNullable::factory()->tomorrow()->create();

        $this->assertFalse($past->isDateRangeActive());
        $this->assertFalse($yesterday->isDateRangeActive());
        $this->assertTrue($active->isDateRangeActive());
        $this->assertTrue($today->isDateRangeActive());
        $this->assertFalse($future->isDateRangeActive());
        $this->assertFalse($tomorrow->isDateRangeActive());
    }

    #[Test]
    public function it_can_check_past(): void
    {
        $past = BothDefaultNameNullable::factory()->past()->create();
        $yesterday = BothDefaultNameNullable::factory()->yesterday()->create();
        $active = BothDefaultNameNullable::factory()->active()->create();
        $today = BothDefaultNameNullable::factory()->today()->create();
        $future = BothDefaultNameNullable::factory()->future()->create();
        $tomorrow = BothDefaultNameNullable::factory()->tomorrow()->create();

        $this->assertTrue($past->isDateRangePast());
        $this->assertTrue($yesterday->isDateRangePast());
        $this->assertFalse($active->isDateRangePast());
        $this->assertFalse($today->isDateRangePast());
        $this->assertFalse($future->isDateRangePast());
        $this->assertFalse($tomorrow->isDateRangePast());
    }

    #[Test]
    public function it_can_check_future(): void
    {
        $past = BothDefaultNameNullable::factory()->past()->create();
        $yesterday = BothDefaultNameNullable::factory()->yesterday()->create();
        $active = BothDefaultNameNullable::factory()->active()->create();
        $today = BothDefaultNameNullable::factory()->today()->create();
        $future = BothDefaultNameNullable::factory()->future()->create();
        $tomorrow = BothDefaultNameNullable::factory()->tomorrow()->create();

        $this->assertFalse($past->isDateRangeFuture());
        $this->assertFalse($yesterday->isDateRangeFuture());
        $this->assertFalse($active->isDateRangeFuture());
        $this->assertFalse($today->isDateRangeFuture());
        $this->assertTrue($future->isDateRangeFuture());
        $this->assertTrue($tomorrow->isDateRangeFuture());
    }

    #[Test]
    public function it_can_query_active(): void
    {
        BothDefaultNameNullable::factory(1)->past()->create();
        BothDefaultNameNullable::factory(2)->yesterday()->create();

        BothDefaultNameNullable::factory(4)->active()->create();
        BothDefaultNameNullable::factory(8)->today()->create();

        BothDefaultNameNullable::factory(16)->future()->create();
        BothDefaultNameNullable::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameNullable> $active */
        $active = BothDefaultNameNullable::whereDateRangeActive()->get();

        $this->assertCount(12, $active);
        $this->assertTrue($active->every(fn (BothDefaultNameNullable $model) => $model->isDateRangeActive()));
    }

    #[Test]
    public function it_can_query_past(): void
    {
        BothDefaultNameNullable::factory(1)->active()->create();
        BothDefaultNameNullable::factory(2)->today()->create();

        BothDefaultNameNullable::factory(4)->past()->create();
        BothDefaultNameNullable::factory(8)->yesterday()->create();

        BothDefaultNameNullable::factory(16)->future()->create();
        BothDefaultNameNullable::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameNullable> $past */
        $past = BothDefaultNameNullable::whereDateRangePast()->get();

        $this->assertCount(12, $past);
        $this->assertTrue($past->every(fn (BothDefaultNameNullable $model) => $model->isDateRangePast()));
    }

    #[Test]
    public function it_can_query_future(): void
    {
        BothDefaultNameNullable::factory(1)->past()->create();
        BothDefaultNameNullable::factory(2)->yesterday()->create();

        BothDefaultNameNullable::factory(4)->future()->create();
        BothDefaultNameNullable::factory(8)->tomorrow()->create();

        BothDefaultNameNullable::factory(16)->active()->create();
        BothDefaultNameNullable::factory(32)->today()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameNullable> $future */
        $future = BothDefaultNameNullable::whereDateRangeFuture()->get();

        $this->assertCount(12, $future);
        $this->assertTrue($future->every(fn (BothDefaultNameNullable $model) => $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_past_or_future(): void
    {
        BothDefaultNameNullable::factory(1)->past()->create();
        BothDefaultNameNullable::factory(2)->yesterday()->create();

        BothDefaultNameNullable::factory(4)->active()->create();
        BothDefaultNameNullable::factory(8)->today()->create();

        BothDefaultNameNullable::factory(16)->future()->create();
        BothDefaultNameNullable::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameNullable> $models */
        $models = BothDefaultNameNullable::whereDateRangePast()->orWhereDateRangeFuture()->get();

        $this->assertCount(51, $models);
        $this->assertTrue($models->every(fn (BothDefaultNameNullable $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameNullable> $models */
        $models = BothDefaultNameNullable::whereNotDateRangeActive()->get();

        $this->assertCount(51, $models);
        $this->assertTrue($models->every(fn (BothDefaultNameNullable $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_date_range_overlap(): void
    {
        $dateRange = DateRange::make(now()->subDays(5), now()->addDays(5));

        $before = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()->subDays(6)]);
        $overlapStart = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()]);
        $within = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDays(2), 'end_date' => now()->addDays(2)]);
        $overlapEnd = BothDefaultNameNullable::factory()->create(['start_date' => now(), 'end_date' => now()->addDays(10)]);
        $after = BothDefaultNameNullable::factory()->create(['start_date' => now()->addDays(6), 'end_date' => now()->addDays(10)]);
        $wider = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()->addDays(10)]);

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameNullable> $models */
        $models = BothDefaultNameNullable::whereDateRangeOverlaps($dateRange)->get();
        $this->assertCount(4, $models);
        $this->assertTrue($models->contains($overlapStart));
        $this->assertTrue($models->contains($within));
        $this->assertTrue($models->contains($overlapEnd));
        $this->assertTrue($models->contains($wider));
    }

    #[Test]
    public function it_can_query_date_range_set_overlap(): void
    {
        $dateRanges = DateRangeSet::make([
            DateRange::make(now()->subDays(10), now()->subDays(5)),
            DateRange::make(now()->addDays(5), now()->addDays(10)),
        ]);

        $overlapFirst = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDays(12), 'end_date' => now()->subDays(6)]);
        $withinFirst = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDays(9), 'end_date' => now()->subDays(7)]);
        $between = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDays(4), 'end_date' => now()->addDays(4)]);
        $withinSecond = BothDefaultNameNullable::factory()->create(['start_date' => now()->addDays(6), 'end_date' => now()->addDays(9)]);
        $overlapSecond = BothDefaultNameNullable::factory()->create(['start_date' => now()->addDays(9), 'end_date' => now()->addDays(12)]);

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameNullable> $models */
        $models = BothDefaultNameNullable::whereDateRangeSetOverlaps($dateRanges)->get();
        $this->assertCount(4, $models);
        $this->assertTrue($models->contains($overlapFirst));
        $this->assertTrue($models->contains($withinFirst));
        $this->assertTrue($models->contains($withinSecond));
        $this->assertTrue($models->contains($overlapSecond));
    }

    #[Test]
    public function it_can_sort_on_date_range(): void
    {
        $d = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDay(), 'end_date' => null]);
        $c = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDay(), 'end_date' => now()->addDay()]);
        $b = BothDefaultNameNullable::factory()->create(['start_date' => now()->subDay(), 'end_date' => now()]);
        $a = BothDefaultNameNullable::factory()->create(['start_date' => null, 'end_date' => now()]);

        $models = BothDefaultNameNullable::orderByDateRange()->get();

        $this->assertCount(4, $models);
        $this->assertEquals($models->pluck('id')->toArray(), [$a->id, $b->id, $c->id, $d->id]);
    }

    #[Test]
    public function it_can_sort_on_active(): void
    {
        BothDefaultNameNullable::factory(3)->past()->create();
        BothDefaultNameNullable::factory(5)->active()->create();
        BothDefaultNameNullable::factory(7)->future()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameNullable> $models */
        $models = BothDefaultNameNullable::orderByDateRangeActive()->get();

        $this->assertCount(15, $models);

        $this->assertTrue($models->slice(0, 5)->every(fn (BothDefaultNameNullable $model) => $model->isDateRangeActive()));
        $this->assertTrue($models->slice(5)->every(fn (BothDefaultNameNullable $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_allows_null_dates(): void
    {
        $model = BothDefaultNameNullable::factory()->create(['start_date' => null, 'end_date' => null]);

        $this->assertNull($model->getStartDate());
        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_removes_time_component_from_dates(): void
    {
        $model = BothDefaultNameNullable::factory()->create([
            'start_date' => now()->setTime(10, 10, 10),
            'end_date' => now()->setTime(20, 20, 20),
        ]);

        $expected = now()->format('Y-m-d');

        $this->assertEquals($expected, $model->start_date?->format('Y-m-d'));
        $this->assertEquals($expected, $model->end_date?->format('Y-m-d'));
    }
}
