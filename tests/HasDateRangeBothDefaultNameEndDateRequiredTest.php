<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Swis\DateRange\DateRange;
use Workbench\App\Models\BothDefaultNameEndDateRequired;

class HasDateRangeBothDefaultNameEndDateRequiredTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_check_active(): void
    {
        $past = BothDefaultNameEndDateRequired::factory()->past()->create();
        $yesterday = BothDefaultNameEndDateRequired::factory()->yesterday()->create();
        $active = BothDefaultNameEndDateRequired::factory()->active()->create();
        $today = BothDefaultNameEndDateRequired::factory()->today()->create();
        $future = BothDefaultNameEndDateRequired::factory()->future()->create();
        $tomorrow = BothDefaultNameEndDateRequired::factory()->tomorrow()->create();

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
        $past = BothDefaultNameEndDateRequired::factory()->past()->create();
        $yesterday = BothDefaultNameEndDateRequired::factory()->yesterday()->create();
        $active = BothDefaultNameEndDateRequired::factory()->active()->create();
        $today = BothDefaultNameEndDateRequired::factory()->today()->create();
        $future = BothDefaultNameEndDateRequired::factory()->future()->create();
        $tomorrow = BothDefaultNameEndDateRequired::factory()->tomorrow()->create();

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
        $past = BothDefaultNameEndDateRequired::factory()->past()->create();
        $yesterday = BothDefaultNameEndDateRequired::factory()->yesterday()->create();
        $active = BothDefaultNameEndDateRequired::factory()->active()->create();
        $today = BothDefaultNameEndDateRequired::factory()->today()->create();
        $future = BothDefaultNameEndDateRequired::factory()->future()->create();
        $tomorrow = BothDefaultNameEndDateRequired::factory()->tomorrow()->create();

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
        BothDefaultNameEndDateRequired::factory(1)->past()->create();
        BothDefaultNameEndDateRequired::factory(2)->yesterday()->create();

        BothDefaultNameEndDateRequired::factory(4)->active()->create();
        BothDefaultNameEndDateRequired::factory(8)->today()->create();

        BothDefaultNameEndDateRequired::factory(16)->future()->create();
        BothDefaultNameEndDateRequired::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameEndDateRequired> $active */
        $active = BothDefaultNameEndDateRequired::whereDateRangeActive()->get();

        $this->assertCount(12, $active);
        $this->assertTrue($active->every(fn (BothDefaultNameEndDateRequired $model) => $model->isDateRangeActive()));
    }

    #[Test]
    public function it_can_query_past(): void
    {
        BothDefaultNameEndDateRequired::factory(1)->active()->create();
        BothDefaultNameEndDateRequired::factory(2)->today()->create();

        BothDefaultNameEndDateRequired::factory(4)->past()->create();
        BothDefaultNameEndDateRequired::factory(8)->yesterday()->create();

        BothDefaultNameEndDateRequired::factory(16)->future()->create();
        BothDefaultNameEndDateRequired::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameEndDateRequired> $past */
        $past = BothDefaultNameEndDateRequired::whereDateRangePast()->get();

        $this->assertCount(12, $past);
        $this->assertTrue($past->every(fn (BothDefaultNameEndDateRequired $model) => $model->isDateRangePast()));
    }

    #[Test]
    public function it_can_query_future(): void
    {
        BothDefaultNameEndDateRequired::factory(1)->past()->create();
        BothDefaultNameEndDateRequired::factory(2)->yesterday()->create();

        BothDefaultNameEndDateRequired::factory(4)->future()->create();
        BothDefaultNameEndDateRequired::factory(8)->tomorrow()->create();

        BothDefaultNameEndDateRequired::factory(16)->active()->create();
        BothDefaultNameEndDateRequired::factory(32)->today()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameEndDateRequired> $future */
        $future = BothDefaultNameEndDateRequired::whereDateRangeFuture()->get();

        $this->assertCount(12, $future);
        $this->assertTrue($future->every(fn (BothDefaultNameEndDateRequired $model) => $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_past_or_future(): void
    {
        BothDefaultNameEndDateRequired::factory(1)->past()->create();
        BothDefaultNameEndDateRequired::factory(2)->yesterday()->create();

        BothDefaultNameEndDateRequired::factory(4)->active()->create();
        BothDefaultNameEndDateRequired::factory(8)->today()->create();

        BothDefaultNameEndDateRequired::factory(16)->future()->create();
        BothDefaultNameEndDateRequired::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameEndDateRequired> $models */
        $models = BothDefaultNameEndDateRequired::whereDateRangePast()->orWhereDateRangeFuture()->get();

        $this->assertCount(51, $models);
        $this->assertTrue($models->every(fn (BothDefaultNameEndDateRequired $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_date_range_overlap(): void
    {
        $dateRange = DateRange::make(now()->subDays(5), now()->addDays(5));

        $before = BothDefaultNameEndDateRequired::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()->subDays(6)]);
        $overlapStart = BothDefaultNameEndDateRequired::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()]);
        $within = BothDefaultNameEndDateRequired::factory()->create(['start_date' => now()->subDays(2), 'end_date' => now()->addDays(2)]);
        $overlapEnd = BothDefaultNameEndDateRequired::factory()->create(['start_date' => now(), 'end_date' => now()->addDays(10)]);
        $after = BothDefaultNameEndDateRequired::factory()->create(['start_date' => now()->addDays(6), 'end_date' => now()->addDays(10)]);
        $wider = BothDefaultNameEndDateRequired::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()->addDays(10)]);

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameEndDateRequired> $models */
        $models = BothDefaultNameEndDateRequired::whereDateRangeOverlaps($dateRange)->get();
        $this->assertCount(4, $models);
        $this->assertTrue($models->contains($overlapStart));
        $this->assertTrue($models->contains($within));
        $this->assertTrue($models->contains($overlapEnd));
        $this->assertTrue($models->contains($wider));
    }

    #[Test]
    public function it_can_sort_on_active(): void
    {
        BothDefaultNameEndDateRequired::factory(3)->past()->create();
        BothDefaultNameEndDateRequired::factory(5)->active()->create();
        BothDefaultNameEndDateRequired::factory(7)->future()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameEndDateRequired> $models */
        $models = BothDefaultNameEndDateRequired::orderByDateRangeActive()->get();

        $this->assertCount(15, $models);

        $this->assertTrue($models->slice(0, 5)->every(fn (BothDefaultNameEndDateRequired $model) => $model->isDateRangeActive()));
        $this->assertTrue($models->slice(5)->every(fn (BothDefaultNameEndDateRequired $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_allows_null_start_date(): void
    {
        $model = BothDefaultNameEndDateRequired::factory()->create(['start_date' => null]);

        $this->assertNull($model->start_date);
        $this->assertNotNull($model->end_date);
    }

    #[Test]
    public function it_removes_time_component_from_dates(): void
    {
        $model = BothDefaultNameEndDateRequired::factory()->create([
            'start_date' => now()->setTime(10, 10, 10),
            'end_date' => now()->setTime(20, 20, 20),
        ]);

        $expected = now()->format('Y-m-d');

        $this->assertEquals($expected, $model->start_date?->format('Y-m-d'));
        $this->assertEquals($expected, $model->end_date?->format('Y-m-d'));
    }
}
