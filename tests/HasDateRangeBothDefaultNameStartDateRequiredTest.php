<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Swis\DateRange\DateRange;
use Workbench\App\Models\BothDefaultNameStartDateRequired;

class HasDateRangeBothDefaultNameStartDateRequiredTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_check_active(): void
    {
        $past = BothDefaultNameStartDateRequired::factory()->past()->create();
        $yesterday = BothDefaultNameStartDateRequired::factory()->yesterday()->create();
        $active = BothDefaultNameStartDateRequired::factory()->active()->create();
        $today = BothDefaultNameStartDateRequired::factory()->today()->create();
        $future = BothDefaultNameStartDateRequired::factory()->future()->create();
        $tomorrow = BothDefaultNameStartDateRequired::factory()->tomorrow()->create();

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
        $past = BothDefaultNameStartDateRequired::factory()->past()->create();
        $yesterday = BothDefaultNameStartDateRequired::factory()->yesterday()->create();
        $active = BothDefaultNameStartDateRequired::factory()->active()->create();
        $today = BothDefaultNameStartDateRequired::factory()->today()->create();
        $future = BothDefaultNameStartDateRequired::factory()->future()->create();
        $tomorrow = BothDefaultNameStartDateRequired::factory()->tomorrow()->create();

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
        $past = BothDefaultNameStartDateRequired::factory()->past()->create();
        $yesterday = BothDefaultNameStartDateRequired::factory()->yesterday()->create();
        $active = BothDefaultNameStartDateRequired::factory()->active()->create();
        $today = BothDefaultNameStartDateRequired::factory()->today()->create();
        $future = BothDefaultNameStartDateRequired::factory()->future()->create();
        $tomorrow = BothDefaultNameStartDateRequired::factory()->tomorrow()->create();

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
        BothDefaultNameStartDateRequired::factory(1)->past()->create();
        BothDefaultNameStartDateRequired::factory(2)->yesterday()->create();

        BothDefaultNameStartDateRequired::factory(4)->active()->create();
        BothDefaultNameStartDateRequired::factory(8)->today()->create();

        BothDefaultNameStartDateRequired::factory(16)->future()->create();
        BothDefaultNameStartDateRequired::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameStartDateRequired> $active */
        $active = BothDefaultNameStartDateRequired::whereDateRangeActive()->get();

        $this->assertCount(12, $active);
        $this->assertTrue($active->every(fn (BothDefaultNameStartDateRequired $model) => $model->isDateRangeActive()));
    }

    #[Test]
    public function it_can_query_past(): void
    {
        BothDefaultNameStartDateRequired::factory(1)->active()->create();
        BothDefaultNameStartDateRequired::factory(2)->today()->create();

        BothDefaultNameStartDateRequired::factory(4)->past()->create();
        BothDefaultNameStartDateRequired::factory(8)->yesterday()->create();

        BothDefaultNameStartDateRequired::factory(16)->future()->create();
        BothDefaultNameStartDateRequired::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameStartDateRequired> $past */
        $past = BothDefaultNameStartDateRequired::whereDateRangePast()->get();

        $this->assertCount(12, $past);
        $this->assertTrue($past->every(fn (BothDefaultNameStartDateRequired $model) => $model->isDateRangePast()));
    }

    #[Test]
    public function it_can_query_future(): void
    {
        BothDefaultNameStartDateRequired::factory(1)->past()->create();
        BothDefaultNameStartDateRequired::factory(2)->yesterday()->create();

        BothDefaultNameStartDateRequired::factory(4)->future()->create();
        BothDefaultNameStartDateRequired::factory(8)->tomorrow()->create();

        BothDefaultNameStartDateRequired::factory(16)->active()->create();
        BothDefaultNameStartDateRequired::factory(32)->today()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameStartDateRequired> $future */
        $future = BothDefaultNameStartDateRequired::whereDateRangeFuture()->get();

        $this->assertCount(12, $future);
        $this->assertTrue($future->every(fn (BothDefaultNameStartDateRequired $model) => $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_past_or_future(): void
    {
        BothDefaultNameStartDateRequired::factory(1)->past()->create();
        BothDefaultNameStartDateRequired::factory(2)->yesterday()->create();

        BothDefaultNameStartDateRequired::factory(4)->active()->create();
        BothDefaultNameStartDateRequired::factory(8)->today()->create();

        BothDefaultNameStartDateRequired::factory(16)->future()->create();
        BothDefaultNameStartDateRequired::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameStartDateRequired> $models */
        $models = BothDefaultNameStartDateRequired::whereDateRangePast()->orWhereDateRangeFuture()->get();

        $this->assertCount(51, $models);
        $this->assertTrue($models->every(fn (BothDefaultNameStartDateRequired $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_date_range_overlap(): void
    {
        $dateRange = DateRange::make(now()->subDays(5), now()->addDays(5));

        $before = BothDefaultNameStartDateRequired::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()->subDays(6)]);
        $overlapStart = BothDefaultNameStartDateRequired::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()]);
        $within = BothDefaultNameStartDateRequired::factory()->create(['start_date' => now()->subDays(2), 'end_date' => now()->addDays(2)]);
        $overlapEnd = BothDefaultNameStartDateRequired::factory()->create(['start_date' => now(), 'end_date' => now()->addDays(10)]);
        $after = BothDefaultNameStartDateRequired::factory()->create(['start_date' => now()->addDays(6), 'end_date' => now()->addDays(10)]);
        $wider = BothDefaultNameStartDateRequired::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()->addDays(10)]);

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameStartDateRequired> $models */
        $models = BothDefaultNameStartDateRequired::whereDateRangeOverlaps($dateRange)->get();
        $this->assertCount(4, $models);
        $this->assertTrue($models->contains($overlapStart));
        $this->assertTrue($models->contains($within));
        $this->assertTrue($models->contains($overlapEnd));
        $this->assertTrue($models->contains($wider));
    }

    #[Test]
    public function it_can_sort_on_active(): void
    {
        BothDefaultNameStartDateRequired::factory(3)->past()->create();
        BothDefaultNameStartDateRequired::factory(5)->active()->create();
        BothDefaultNameStartDateRequired::factory(7)->future()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameStartDateRequired> $models */
        $models = BothDefaultNameStartDateRequired::orderByDateRangeActive()->get();

        $this->assertCount(15, $models);

        $this->assertTrue($models->slice(0, 5)->every(fn (BothDefaultNameStartDateRequired $model) => $model->isDateRangeActive()));
        $this->assertTrue($models->slice(5)->every(fn (BothDefaultNameStartDateRequired $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_allows_null_end_date(): void
    {
        $model = BothDefaultNameStartDateRequired::factory()->create(['end_date' => null]);

        $this->assertNotNull($model->start_date);
        $this->assertNull($model->end_date);
    }

    #[Test]
    public function it_removes_time_component_from_dates(): void
    {
        $model = BothDefaultNameStartDateRequired::factory()->create([
            'start_date' => now()->setTime(10, 10, 10),
            'end_date' => now()->setTime(20, 20, 20),
        ]);

        $expected = now()->format('Y-m-d');

        $this->assertEquals($expected, $model->start_date?->format('Y-m-d'));
        $this->assertEquals($expected, $model->end_date?->format('Y-m-d'));
    }
}
