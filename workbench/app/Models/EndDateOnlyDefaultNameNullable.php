<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Swis\DateRange\Eloquent\Concerns\HasDateRange;

class EndDateOnlyDefaultNameNullable extends Model
{
    use HasDateRange;

    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Database\Factories\EndDateOnlyDefaultNameNullableFactory> */
    use HasFactory;

    protected $fillable = [
        'end_date',
    ];

    protected $table = 'end_date_only_default_name_nullable';

    public function getStartDateColumn(): ?string
    {
        return null;
    }
}
