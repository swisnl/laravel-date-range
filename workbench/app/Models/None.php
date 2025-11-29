<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Swis\DateRange\Eloquent\Concerns\HasDateRange;

class None extends Model
{
    use HasDateRange;

    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Workbench\Database\Factories\NoneFactory> */
    use HasFactory;

    protected $table = 'none';

    public function getStartDateColumn(): ?string
    {
        return null;
    }

    public function getEndDateColumn(): ?string
    {
        return null;
    }
}
