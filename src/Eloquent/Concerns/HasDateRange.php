<?php

namespace Swis\DateRange\Eloquent\Concerns;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Illuminate\Database\Eloquent\Attributes\Initialize;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use Swis\DateRange\DateRange;

/**
 * Eloquent model concern for date ranges.
 *
 * Models using this trait have a date range attached to them, defined by an
 * optional start and end date.
 */
trait HasDateRange
{
    use HasDateRangeSetScope;

    const DATE_RANGE_START_COLUMN = 'start_date';

    const DATE_RANGE_END_COLUMN = 'end_date';

    /**
     * Get the name of the "start date" column.
     */
    public function getStartDateColumn(): ?string
    {
        return static::DATE_RANGE_START_COLUMN;
    }

    /**
     * Get the fully qualified "start date" column.
     */
    public function getQualifiedStartDateColumn(): ?string
    {
        $startDateColumn = $this->getStartDateColumn();

        return $startDateColumn ? $this->qualifyColumn($startDateColumn) : null;
    }

    /**
     * Get the start date.
     */
    public function getStartDate(): ?CarbonImmutable
    {
        $startDateColumn = $this->getStartDateColumn();

        return $startDateColumn ? $this->{$startDateColumn} : null;
    }

    /**
     * Set the start date.
     */
    public function setStartDate(DateTimeInterface|string|null $startDate): void
    {
        $startDateColumn = $this->getStartDateColumn();

        if (! $startDateColumn) {
            if ($startDate !== null) {
                throw new InvalidArgumentException('Start date column is not defined.');
            }

            return;
        }

        $this->{$startDateColumn} = $startDate ? CarbonImmutable::parse($startDate)->startOfDay() : null;
    }

    /**
     * Get the name of the "end date" column.
     */
    public function getEndDateColumn(): ?string
    {
        return static::DATE_RANGE_END_COLUMN;
    }

    /**
     * Get the fully qualified "end date" column.
     */
    public function getQualifiedEndDateColumn(): ?string
    {
        $endDateColumn = $this->getEndDateColumn();

        return $endDateColumn ? $this->qualifyColumn($endDateColumn) : null;
    }

    /**
     * Get the end date.
     */
    public function getEndDate(): ?CarbonImmutable
    {
        $endDateColumn = $this->getEndDateColumn();

        return $endDateColumn ? $this->{$endDateColumn} : null;
    }

    /**
     * Set the end date.
     */
    public function setEndDate(DateTimeInterface|string|null $endDate): void
    {
        $endDateColumn = $this->getEndDateColumn();

        if (! $endDateColumn) {
            if ($endDate !== null) {
                throw new InvalidArgumentException('End date column is not defined.');
            }

            return;
        }

        $this->{$endDateColumn} = $endDate ? CarbonImmutable::parse($endDate)->startOfDay() : null;
    }

    /**
     * Get the date range.
     *
     * Uses the start date and end date of the model to create a DateRange
     * object.
     */
    public function getDateRange(): DateRange
    {
        return DateRange::make($this->getStartDate(), $this->getEndDate());
    }

    /**
     * Set the date range.
     *
     * Uses a DateRange object to set the start date and end date of the model.
     */
    public function setDateRange(DateRange $dateRange): void
    {
        $this->setStartDate($dateRange->getStartDate());
        $this->setEndDate($dateRange->getEndDate());
    }

    #[Boot]
    protected static function bootHasDateRange(): void
    {
        static::saving(function ($model) {
            $startDateColumn = $model->getStartDateColumn();
            if ($startDateColumn) {
                $model->{$startDateColumn} = $model->{$startDateColumn}?->startOfDay();
            }

            $endDateColumn = $model->getEndDateColumn();
            if ($endDateColumn) {
                $model->{$endDateColumn} = $model->{$endDateColumn}?->startOfDay();
            }

            // Make sure the date range is valid before saving the model.
            $model->validateDateRange();
        });
    }

    #[Initialize]
    protected function initializeHasDateRange(): void
    {
        $startDateColumn = $this->getStartDateColumn();
        if ($startDateColumn) {
            $this->casts[$startDateColumn] = 'immutable_date';
        }

        $endDateColumn = $this->getEndDateColumn();
        if ($endDateColumn) {
            $this->casts[$endDateColumn] = 'immutable_date';
        }
    }

    /**
     * Order by start date and end date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orderByDateRange(Builder $query, string $direction = 'asc'): void
    {
        $startDateColumn = $this->getQualifiedStartDateColumn();
        if ($startDateColumn) {
            $query->orderBy($startDateColumn, $direction);
        }

        $endDateColumn = $this->getQualifiedEndDateColumn();
        if ($endDateColumn) {
            $query->orderBy($endDateColumn, $direction);
        }
    }

    /**
     * Scope a query to only include models that overlap with a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function whereDateRangeOverlaps(Builder $query, DateRange $dateRange, string $boolean = 'and'): void
    {
        $query->where(function (Builder $query) use ($dateRange) {
            $startDateColumn = $this->getQualifiedStartDateColumn();
            $endDateColumn = $this->getQualifiedEndDateColumn();

            $startDate = $dateRange->getStartDate();
            $endDate = $dateRange->getEndDate();

            $conditionsAdded = false;

            if ($startDateColumn && $endDate) {
                $query->where(function (Builder $query) use ($endDate, $startDateColumn) {
                    $query
                        ->where($startDateColumn, '<=', $endDate)
                        ->orWhereNull($startDateColumn);
                });
                $conditionsAdded = true;
            }

            if ($endDateColumn && $startDate) {
                $query->where(function (Builder $query) use ($startDate, $endDateColumn) {
                    $query
                        ->where($endDateColumn, '>=', $startDate)
                        ->orWhereNull($endDateColumn);
                });
                $conditionsAdded = true;
            }

            if (! $conditionsAdded) {
                $query->whereRaw('1 = 1');
            }
        }, null, null, $boolean);
    }

    /**
     * Scope a query to only include models that overlap with a date range,
     * using "or where".
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWhereDateRangeOverlaps(Builder $query, DateRange $dateRange): void
    {
        $this->whereDateRangeOverlaps($query, $dateRange, 'or');
    }

    /**
     * Scope a query to not include models that overlap with a date range,
     * using "where not".
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function whereNotDateRangeOverlaps(Builder $query, DateRange $dateRange, string $boolean = 'and'): void
    {
        $this->whereDateRangeOverlaps($query, $dateRange, $boolean.' not');
    }

    /**
     * Scope a query to not include models that overlap with a date range,
     * using "or where not".
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWhereNotDateRangeOverlaps(Builder $query, DateRange $dateRange): void
    {
        $this->whereNotDateRangeOverlaps($query, $dateRange, 'or');
    }

    /**
     * Scope a query to only include models that are active on a given date.
     *
     * Active is defined as the date range of the model including the given
     * date. If no date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function whereActive(Builder $query, DateTimeInterface|string|null $date = null, string $boolean = 'and'): void
    {
        $date = $date ? CarbonImmutable::parse($date)->startOfDay() : CarbonImmutable::now()->startOfDay();

        $query->where(function (Builder $query) use ($date) {
            $startDateColumn = $this->getQualifiedStartDateColumn();
            $endDateColumn = $this->getQualifiedEndDateColumn();

            $conditionsAdded = false;

            if ($startDateColumn) {
                $query->where(function (Builder $query) use ($date, $startDateColumn) {
                    $query
                        ->where($startDateColumn, '<=', $date)
                        ->orWhereNull($startDateColumn);
                });
                $conditionsAdded = true;
            }

            if ($endDateColumn) {
                $query->where(function (Builder $query) use ($date, $endDateColumn) {
                    $query
                        ->where($endDateColumn, '>=', $date)
                        ->orWhereNull($endDateColumn);
                });
                $conditionsAdded = true;
            }

            if (! $conditionsAdded) {
                $query->whereRaw('1 = 1');
            }
        }, null, null, $boolean);
    }

    /**
     * Scope a query to only include models that are active on a given date,
     * using "or where".
     *
     * Active is defined as the date range of the model including the given
     * date. If no date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWhereActive(Builder $query, DateTimeInterface|string|null $date = null): void
    {
        $this->whereActive($query, $date, 'or');
    }

    /**
     * Scope a query to only include models that are not active on a given date,
     * using "where not".
     *
     * Active is defined as the date range of the model including the given
     * date. If no date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function whereNotActive(Builder $query, DateTimeInterface|string|null $date = null, string $boolean = 'and'): void
    {
        $this->whereActive($query, $date, $boolean.' not');
    }

    /**
     * Scope a query to only include models that are not active on a given date,
     * using "or where not".
     *
     * Active is defined as the date range of the model including the given
     * date. If no date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWhereNotActive(Builder $query, DateTimeInterface|string|null $date = null): void
    {
        $this->whereNotActive($query, $date, 'or');
    }

    /**
     * Scope a query to only include models that are past on a given date.
     *
     * Past is defined as the date being after the end date of the model. If no
     * date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function wherePast(Builder $query, DateTimeInterface|string|null $date = null, string $boolean = 'and'): void
    {
        $date = $date ? CarbonImmutable::parse($date)->startOfDay() : CarbonImmutable::now()->startOfDay();

        $query->where(function (Builder $query) use ($date) {
            $endDateColumn = $this->getQualifiedEndDateColumn();

            if ($endDateColumn) {
                $query->where($endDateColumn, '<', $date);
            } else {
                $query->whereRaw('1 = 0');
            }
        }, null, null, $boolean);
    }

    /**
     * Scope a query to only include models that are past on a given date, using
     * "or where".
     *
     * Past is defined as the date being after the end date of the model. If no
     * date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWherePast(Builder $query, DateTimeInterface|string|null $date = null): void
    {
        $this->wherePast($query, $date, 'or');
    }

    /**
     * Scope a query to not include models that are past on a given date, using
     * "where not".
     *
     * Past is defined as the date being after the end date of the model. If no
     * date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function whereNotPast(Builder $query, DateTimeInterface|string|null $date = null, string $boolean = 'and'): void
    {
        $this->wherePast($query, $date, $boolean.' not');
    }

    /**
     * Scope a query to not include models that are past on a given date, using
     * "or where not".
     *
     * Past is defined as the date being after the end date of the model. If no
     * date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWhereNotPast(Builder $query, DateTimeInterface|string|null $date = null, string $boolean = 'and'): void
    {
        $this->whereNotPast($query, $date, 'or');
    }

    /**
     * Scope a query to only include models that are future on a given date.
     *
     * Future is defined as the date being before the start date of the model.
     * If no date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function whereFuture(Builder $query, DateTimeInterface|string|null $date = null, string $boolean = 'and'): void
    {
        $date = $date ? CarbonImmutable::parse($date)->startOfDay() : CarbonImmutable::now()->startOfDay();

        $query->where(function (Builder $query) use ($date) {
            $startDateColumn = $this->getQualifiedStartDateColumn();

            if ($startDateColumn) {
                $query->where($startDateColumn, '>', $date);
            } else {
                $query->whereRaw('1 = 0');
            }
        }, null, null, $boolean);
    }

    /**
     * Scope a query to only include models that are future on a given date,
     * using "or where".
     *
     * Future is defined as the date being before the start date of the model.
     * If no date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWhereFuture(Builder $query, DateTimeInterface|string|null $date = null): void
    {
        $this->whereFuture($query, $date, 'or');
    }

    /**
     * Scope a query to not include models that are future on a given date,
     * using "where not".
     *
     * Future is defined as the date being before the start date of the model.
     * If no date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function whereNotFuture(Builder $query, DateTimeInterface|string|null $date = null, string $boolean = 'and'): void
    {
        $this->whereFuture($query, $date, $boolean.' not');
    }

    /**
     * Scope a query to not include models that are future on a given date,
     * using "or where not".
     *
     * Future is defined as the date being before the start date of the model.
     * If no date is given, the current date is used.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orWhereNotFuture(Builder $query, DateTimeInterface|string|null $date = null): void
    {
        $this->whereNotFuture($query, $date, 'or');
    }

    /**
     * Order by active status on a given date.
     *
     * Active is defined as the date range of the model including the given
     * date. If no date is given, the current date is used. Active models will
     * be shown first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    #[Scope]
    protected function orderByActive(Builder $query, DateTimeInterface|string|null $date = null, string $direction = 'desc'): void
    {
        if (! in_array($direction, ['asc', 'desc'], true)) {
            throw new InvalidArgumentException('Order direction must be "asc" or "desc".');
        }

        $date = $date ? CarbonImmutable::parse($date)->startOfDay() : CarbonImmutable::now()->startOfDay();

        $direction = strtoupper($direction);

        $startDateColumn = $this->getQualifiedStartDateColumn();
        $endDateColumn = $this->getQualifiedEndDateColumn();

        $cases = [];
        $bindings = [];

        if ($startDateColumn) {
            $cases[] = "({$startDateColumn} <= ? OR {$startDateColumn} IS NULL)";
            $bindings[] = $date;
        }

        if ($endDateColumn) {
            $cases[] = "({$endDateColumn} >= ? OR {$endDateColumn} IS NULL)";
            $bindings[] = $date;
        }

        if ($cases) {
            $query->orderByRaw(
                'CASE WHEN '.implode(' AND ', $cases)." THEN 1 ELSE 0 END {$direction}",
                $bindings
            );
        }
    }

    /**
     * Determine if the model is active on a given date.
     *
     * Active is defined as the date range of the model including the given
     * date. If no date is given, the current date is used.
     */
    public function isActive(DateTimeInterface|string|null $date = null): bool
    {
        return $this->getDateRange()->inRange($date ?? now());
    }

    /**
     * Determine if the model is not active on a given date.
     *
     * Active is defined as the date range of the model including the given
     * date. If no date is given, the current date is used.
     */
    public function isNotActive(DateTimeInterface|string|null $date = null): bool
    {
        return ! $this->isActive($date);
    }

    /**
     * Determine if the model is past on a given date.
     *
     * Past is defined as the date being after the end date of the model. If no
     * date is given, the current date is used.
     */
    public function isPast(DateTimeInterface|string|null $date = null): bool
    {
        $date = $date ? CarbonImmutable::parse($date)->startOfDay() : CarbonImmutable::now()->startOfDay();

        $endDate = $this->getEndDate();

        return isset($endDate) && $date > $endDate;
    }

    /**
     * Determine if the model is not past on a given date.
     *
     * Past is defined as the date being after the end date of the model. If no
     * date is given, the current date is used.
     */
    public function isNotPast(DateTimeInterface|string|null $date = null): bool
    {
        return ! $this->isPast($date);
    }

    /**
     * Determine if the model is future on a given date.
     *
     * Future is defined as the date being before the start date of the model.
     * If no date is given, the current date is used.
     */
    public function isFuture(DateTimeInterface|string|null $date = null): bool
    {
        $date = $date ? CarbonImmutable::parse($date)->startOfDay() : CarbonImmutable::now()->startOfDay();

        $startDate = $this->getStartDate();

        return isset($startDate) && $date < $startDate;
    }

    /**
     * Determine if the model is not future on a given date.
     *
     * Future is defined as the date being before the start date of the model.
     * If no date is given, the current date is used.
     */
    public function isNotFuture(DateTimeInterface|string|null $date = null): bool
    {
        return ! $this->isFuture($date);
    }

    /**
     * Validate the date range.
     *
     * Ensures that the start date is not after the end date.
     *
     * @throws \InvalidArgumentException
     */
    public function validateDateRange(): void
    {
        $this->getDateRange();
    }
}
