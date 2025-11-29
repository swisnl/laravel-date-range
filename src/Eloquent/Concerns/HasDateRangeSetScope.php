<?php

namespace Swis\DateRange\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Swis\DateRange\DateRangeSet;

trait HasDateRangeSetScope
{
    /**
     * Scope a query to only include models where the date range or date range
     * set overlaps with the given date range set.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function whereDateRangeSetOverlaps(Builder $query, DateRangeSet $dateRangeSet, string $boolean = 'and'): void
    {
        $query->where(function (Builder $query) use ($dateRangeSet) {
            if ($dateRangeSet->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                foreach ($dateRangeSet->getDateRanges() as $dateRange) {
                    $this->orWhereDateRangeOverlaps($query, $dateRange);
                }
            }
        }, null, null, $boolean);
    }

    /**
     * Scope a query to only include models where the date range or date range
     * set overlaps with the given date range set, using "or where".
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWhereDateRangeSetOverlaps(Builder $query, DateRangeSet $dateRangeSet): void
    {
        $this->whereDateRangeSetOverlaps($query, $dateRangeSet, 'or');
    }

    /**
     * Scope a query to not include models where the date range or date range
     * set overlaps with the given date range set, using "where not".
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function whereNotDateRangeSetOverlaps(Builder $query, DateRangeSet $dateRangeSet, string $boolean = 'and'): void
    {
        $this->whereDateRangeSetOverlaps($query, $dateRangeSet, $boolean.' not');
    }

    /**
     * Scope a query to not include models where the date range or date range
     * set overlaps with the given date range set, using "or where not".
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWhereNotDateRangeSetOverlaps(Builder $query, DateRangeSet $dateRangeSet): void
    {
        $this->whereNotDateRangeSetOverlaps($query, $dateRangeSet, 'or');
    }
}
