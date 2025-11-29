<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Swis\DateRange\Eloquent\Concerns\HasDateRange;

class StartDateOnlyDefaultNameNullable extends Model
{
    use HasDateRange;

    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Database\Factories\StartDateOnlyDefaultNameNullableFactory> */
    use HasFactory;

    protected $fillable = [
        'start_date',
    ];

    protected $table = 'start_date_only_default_name_nullable';

    public function getEndDateColumn(): ?string
    {
        return null;
    }
}
