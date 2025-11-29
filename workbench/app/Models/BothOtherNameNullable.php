<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Swis\DateRange\Eloquent\Concerns\HasDateRange;

class BothOtherNameNullable extends Model
{
    use HasDateRange;

    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Database\Factories\BothOtherNameNullableFactory> */
    use HasFactory;

    protected $fillable = [
        'foo',
        'bar',
    ];

    protected $table = 'both_other_name_nullable';

    public function getStartDateColumn(): ?string
    {
        return 'foo';
    }

    public function getEndDateColumn(): ?string
    {
        return 'bar';
    }
}
