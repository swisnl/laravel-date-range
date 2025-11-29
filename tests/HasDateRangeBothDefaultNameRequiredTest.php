<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Swis\DateRange\DateRange;
use Workbench\App\Models\BothDefaultNameRequired;

class HasDateRangeBothDefaultNameRequiredTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_check_active(): void
    {
        $past = BothDefaultNameRequired::factory()->past()->create();
        $yesterday = BothDefaultNameRequired::factory()->yesterday()->create();
        $active = BothDefaultNameRequired::factory()->active()->create();
        $today = BothDefaultNameRequired::factory()->today()->create();
        $future = BothDefaultNameRequired::factory()->future()->create();
        $tomorrow = BothDefaultNameRequired::factory()->tomorrow()->create();

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
        $past = BothDefaultNameRequired::factory()->past()->create();
        $yesterday = BothDefaultNameRequired::factory()->yesterday()->create();
        $active = BothDefaultNameRequired::factory()->active()->create();
        $today = BothDefaultNameRequired::factory()->today()->create();
        $future = BothDefaultNameRequired::factory()->future()->create();
        $tomorrow = BothDefaultNameRequired::factory()->tomorrow()->create();

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
        $past = BothDefaultNameRequired::factory()->past()->create();
        $yesterday = BothDefaultNameRequired::factory()->yesterday()->create();
        $active = BothDefaultNameRequired::factory()->active()->create();
        $today = BothDefaultNameRequired::factory()->today()->create();
        $future = BothDefaultNameRequired::factory()->future()->create();
        $tomorrow = BothDefaultNameRequired::factory()->tomorrow()->create();

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
        BothDefaultNameRequired::factory(1)->past()->create();
        BothDefaultNameRequired::factory(2)->yesterday()->create();

        BothDefaultNameRequired::factory(4)->active()->create();
        BothDefaultNameRequired::factory(8)->today()->create();

        BothDefaultNameRequired::factory(16)->future()->create();
        BothDefaultNameRequired::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameRequired> $active */
        $active = BothDefaultNameRequired::whereDateRangeActive()->get();

        $this->assertCount(12, $active);
        $this->assertTrue($active->every(fn (BothDefaultNameRequired $model) => $model->isDateRangeActive()));
    }

    #[Test]
    public function it_can_query_past(): void
    {
        BothDefaultNameRequired::factory(1)->active()->create();
        BothDefaultNameRequired::factory(2)->today()->create();

        BothDefaultNameRequired::factory(4)->past()->create();
        BothDefaultNameRequired::factory(8)->yesterday()->create();

        BothDefaultNameRequired::factory(16)->future()->create();
        BothDefaultNameRequired::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameRequired> $past */
        $past = BothDefaultNameRequired::whereDateRangePast()->get();

        $this->assertCount(12, $past);
        $this->assertTrue($past->every(fn (BothDefaultNameRequired $model) => $model->isDateRangePast()));
    }

    #[Test]
    public function it_can_query_future(): void
    {
        BothDefaultNameRequired::factory(1)->past()->create();
        BothDefaultNameRequired::factory(2)->yesterday()->create();

        BothDefaultNameRequired::factory(4)->future()->create();
        BothDefaultNameRequired::factory(8)->tomorrow()->create();

        BothDefaultNameRequired::factory(16)->active()->create();
        BothDefaultNameRequired::factory(32)->today()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameRequired> $future */
        $future = BothDefaultNameRequired::whereDateRangeFuture()->get();

        $this->assertCount(12, $future);
        $this->assertTrue($future->every(fn (BothDefaultNameRequired $model) => $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_past_or_future(): void
    {
        BothDefaultNameRequired::factory(1)->past()->create();
        BothDefaultNameRequired::factory(2)->yesterday()->create();

        BothDefaultNameRequired::factory(4)->active()->create();
        BothDefaultNameRequired::factory(8)->today()->create();

        BothDefaultNameRequired::factory(16)->future()->create();
        BothDefaultNameRequired::factory(32)->tomorrow()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameRequired> $models */
        $models = BothDefaultNameRequired::whereDateRangePast()->orWhereDateRangeFuture()->get();

        $this->assertCount(51, $models);
        $this->assertTrue($models->every(fn (BothDefaultNameRequired $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_can_query_date_range_overlap(): void
    {
        $dateRange = DateRange::make(now()->subDays(5), now()->addDays(5));

        $before = BothDefaultNameRequired::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()->subDays(6)]);
        $overlapStart = BothDefaultNameRequired::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()]);
        $within = BothDefaultNameRequired::factory()->create(['start_date' => now()->subDays(2), 'end_date' => now()->addDays(2)]);
        $overlapEnd = BothDefaultNameRequired::factory()->create(['start_date' => now(), 'end_date' => now()->addDays(10)]);
        $after = BothDefaultNameRequired::factory()->create(['start_date' => now()->addDays(6), 'end_date' => now()->addDays(10)]);
        $wider = BothDefaultNameRequired::factory()->create(['start_date' => now()->subDays(10), 'end_date' => now()->addDays(10)]);

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameRequired> $models */
        $models = BothDefaultNameRequired::whereDateRangeOverlaps($dateRange)->get();
        $this->assertCount(4, $models);
        $this->assertTrue($models->contains($overlapStart));
        $this->assertTrue($models->contains($within));
        $this->assertTrue($models->contains($overlapEnd));
        $this->assertTrue($models->contains($wider));
    }

    #[Test]
    public function it_can_sort_on_active(): void
    {
        BothDefaultNameRequired::factory(3)->past()->create();
        BothDefaultNameRequired::factory(5)->active()->create();
        BothDefaultNameRequired::factory(7)->future()->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\BothDefaultNameRequired> $models */
        $models = BothDefaultNameRequired::orderByDateRangeActive()->get();

        $this->assertCount(15, $models);

        $this->assertTrue($models->slice(0, 5)->every(fn (BothDefaultNameRequired $model) => $model->isDateRangeActive()));
        $this->assertTrue($models->slice(5)->every(fn (BothDefaultNameRequired $model) => $model->isDateRangePast() || $model->isDateRangeFuture()));
    }

    #[Test]
    public function it_removes_time_component_from_dates(): void
    {
        $model = BothDefaultNameRequired::factory()->create([
            'start_date' => now()->setTime(10, 10, 10),
            'end_date' => now()->setTime(20, 20, 20),
        ]);

        $expected = now()->format('Y-m-d');

        $this->assertEquals($expected, $model->start_date?->format('Y-m-d'));
        $this->assertEquals($expected, $model->end_date?->format('Y-m-d'));
    }
}
