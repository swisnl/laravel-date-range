<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Swis\DateRange\DateRange;
use Workbench\App\Models\None;

class HasDateRangeNoneTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_check_active(): void
    {
        $model = None::factory()->create();

        $this->assertTrue($model->isDateRangeActive());
    }

    #[Test]
    public function it_can_check_past(): void
    {
        $model = None::factory()->create();

        $this->assertFalse($model->isDateRangePast());
    }

    #[Test]
    public function it_can_check_future(): void
    {
        $model = None::factory()->create();

        $this->assertFalse($model->isDateRangeFuture());
    }

    #[Test]
    public function it_can_query_active(): void
    {
        None::factory(4)->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\None> $active */
        $active = None::whereDateRangeActive()->get();

        $this->assertCount(4, $active);
        $this->assertTrue($active->every(fn (None $model) => $model->isDateRangeActive()));
    }

    #[Test]
    public function it_can_query_past(): void
    {
        None::factory(4)->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\None> $past */
        $past = None::whereDateRangePast()->get();

        $this->assertCount(0, $past);
    }

    #[Test]
    public function it_can_query_future(): void
    {
        None::factory(4)->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\None> $future */
        $future = None::whereDateRangeFuture()->get();

        $this->assertCount(0, $future);
    }

    #[Test]
    public function it_can_query_date_range_overlap(): void
    {
        $dateRange = DateRange::make(now()->subDays(5), now()->addDays(5));

        None::factory(4)->create();

        /** @var \Illuminate\Support\Collection<array-key, \Workbench\App\Models\None> $models */
        $models = None::whereDateRangeOverlaps($dateRange)->get();
        $this->assertCount(4, $models);
    }

    #[Test]
    public function it_gets_start_date(): void
    {
        $model = None::factory()->create();

        $this->assertNull($model->getStartDate());
    }

    #[Test]
    public function it_gets_end_date(): void
    {
        $model = None::factory()->create();

        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_accepts_null_start_date(): void
    {
        $model = new None;
        $model->setStartDate(null);

        $this->assertNull($model->getStartDate());
    }

    #[Test]
    public function it_accepts_null_end_date(): void
    {
        $model = new None;
        $model->setEndDate(null);

        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_errors_on_setting_start_date(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $model = new None;
        $model->setStartDate(now());
    }

    #[Test]
    public function it_errors_on_setting_end_date(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $model = new None;
        $model->setEndDate(now());
    }

    #[Test]
    public function it_accepts_open_date_range(): void
    {
        $model = None::factory()->create();

        $model->setDateRange(DateRange::make());

        $this->assertNull($model->getStartDate());
        $this->assertNull($model->getEndDate());
    }

    #[Test]
    public function it_errors_on_setting_closed_date_range(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $model = new None;
        $model->setDateRange(DateRange::make(now(), now()->addDays(5)));
    }
}
