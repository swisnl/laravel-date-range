<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Swis\DateRange\DateRange;
use Workbench\App\Models\BothOtherNameNullable;

class HasDateRangeBothOtherNameNullableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_check_active(): void
    {
        $past = BothOtherNameNullable::factory()->past()->create();
        $yesterday = BothOtherNameNullable::factory()->yesterday()->create();
        $active = BothOtherNameNullable::factory()->active()->create();
        $today = BothOtherNameNullable::factory()->today()->create();
        $future = BothOtherNameNullable::factory()->future()->create();
        $tomorrow = BothOtherNameNullable::factory()->tomorrow()->create();

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
        $past = BothOtherNameNullable::factory()->past()->create();
        $yesterday = BothOtherNameNullable::factory()->yesterday()->create();
        $active = BothOtherNameNullable::factory()->active()->create();
        $today = BothOtherNameNullable::factory()->today()->create();
        $future = BothOtherNameNullable::factory()->future()->create();
        $tomorrow = BothOtherNameNullable::factory()->tomorrow()->create();

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
        $past = BothOtherNameNullable::factory()->past()->create();
        $yesterday = BothOtherNameNullable::factory()->yesterday()->create();
        $active = BothOtherNameNullable::factory()->active()->create();
        $today = BothOtherNameNullable::factory()->today()->create();
        $future = BothOtherNameNullable::factory()->future()->create();
        $tomorrow = BothOtherNameNullable::factory()->tomorrow()->create();

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
        BothOtherNameNullable::factory(1)->past()->create();
        BothOtherNameNullable::factory(2)->yesterday()->create();

        BothOtherNameNullable::factory(4)->active()->create();
        BothOtherNameNullable::factory(8)->today()->create();

        BothOtherNameNullable::factory(16)->future()->create();
        BothOtherNameNullable::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothOtherNameNullable> $active */
        $active = BothOtherNameNullable::whereDateRangeActive()->get();

        $this->assertCount(12, $active);
        $this->assertTrue($active->every(fn (BothOtherNameNullable $model) => $model->isDateRangeActive()));
    }

    #[Test]
    public function it_can_query_past(): void
    {
        BothOtherNameNullable::factory(1)->active()->create();
        BothOtherNameNullable::factory(2)->today()->create();

        BothOtherNameNullable::factory(4)->past()->create();
        BothOtherNameNullable::factory(8)->yesterday()->create();

        BothOtherNameNullable::factory(16)->future()->create();
        BothOtherNameNullable::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothOtherNameNullable> $past */
        $past = BothOtherNameNullable::whereDateRangePast()->get();

        $this->assertCount(12, $past);
        $this->assertTrue($past->every(fn (BothOtherNameNullable $model) => $model->isDateRangePast()));
    }

    #[Test]
    public function it_can_query_future(): void
    {
        BothOtherNameNullable::factory(1)->past()->create();
        BothOtherNameNullable::factory(2)->yesterday()->create();

        BothOtherNameNullable::factory(4)->future()->create();
        BothOtherNameNullable::factory(8)->tomorrow()->create();

        BothOtherNameNullable::factory(16)->active()->create();
        BothOtherNameNullable::factory(32)->today()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothOtherNameNullable> $future */
        $future = BothOtherNameNullable::whereDateRangeFuture()->get();

        $this->assertCount(12, $future);
        $this->assertTrue($future->every(fn (BothOtherNameNullable $model) => $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_past_or_future(): void
    {
        BothOtherNameNullable::factory(1)->past()->create();
        BothOtherNameNullable::factory(2)->yesterday()->create();

        BothOtherNameNullable::factory(4)->active()->create();
        BothOtherNameNullable::factory(8)->today()->create();

        BothOtherNameNullable::factory(16)->future()->create();
        BothOtherNameNullable::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothOtherNameNullable> $models */
        $models = BothOtherNameNullable::whereDateRangePast()->orWhereDateRangeFuture()->get();

        $this->assertCount(51, $models);
        $this->assertTrue($models->every(fn (BothOtherNameNullable $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_date_range_overlap(): void
    {
        $dateRange = DateRange::make(now()->subDays(5), now()->addDays(5));

        $before = BothOtherNameNullable::factory()->create(['foo' => now()->subDays(10), 'bar' => now()->subDays(6)]);
        $overlapStart = BothOtherNameNullable::factory()->create(['foo' => now()->subDays(10), 'bar' => now()]);
        $within = BothOtherNameNullable::factory()->create(['foo' => now()->subDays(2), 'bar' => now()->addDays(2)]);
        $overlapEnd = BothOtherNameNullable::factory()->create(['foo' => now(), 'bar' => now()->addDays(10)]);
        $after = BothOtherNameNullable::factory()->create(['foo' => now()->addDays(6), 'bar' => now()->addDays(10)]);
        $wider = BothOtherNameNullable::factory()->create(['foo' => now()->subDays(10), 'bar' => now()->addDays(10)]);

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothOtherNameNullable> $models */
        $models = BothOtherNameNullable::whereDateRangeOverlaps($dateRange)->get();
        $this->assertCount(4, $models);
        $this->assertTrue($models->contains($overlapStart));
        $this->assertTrue($models->contains($within));
        $this->assertTrue($models->contains($overlapEnd));
        $this->assertTrue($models->contains($wider));
    }

    #[Test]
    public function it_can_sort_on_active(): void
    {
        BothOtherNameNullable::factory(3)->past()->create();
        BothOtherNameNullable::factory(5)->active()->create();
        BothOtherNameNullable::factory(7)->future()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothOtherNameNullable> $models */
        $models = BothOtherNameNullable::orderByDateRangeActive()->get();

        $this->assertCount(15, $models);

        $this->assertTrue($models->slice(0, 5)->every(fn (BothOtherNameNullable $model) => $model->isDateRangeActive()));
        $this->assertTrue($models->slice(5)->every(fn (BothOtherNameNullable $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_allows_null_dates(): void
    {
        $model = BothOtherNameNullable::factory()->create(['foo' => null, 'bar' => null]);

        $this->assertNull($model->getStartDate());
        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_removes_time_component_from_dates(): void
    {
        $model = BothOtherNameNullable::factory()->create([
            'foo' => now()->setTime(10, 10, 10),
            'bar' => now()->setTime(20, 20, 20),
        ]);

        $expected = now()->format('Y-m-d');

        $this->assertEquals($expected, $model->foo?->format('Y-m-d'));
        $this->assertEquals($expected, $model->bar?->format('Y-m-d'));
    }
}
