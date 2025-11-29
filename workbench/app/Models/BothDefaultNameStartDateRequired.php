<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Swis\DateRange\Eloquent\Concerns\HasDateRange;

class BothDefaultNameStartDateRequired extends Model
{
    use HasDateRange;

    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Database\Factories\BothDefaultNameStartDateRequiredFactory> */
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
    ];

    protected $table = 'both_default_name_start_date_required';
}
