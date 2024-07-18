<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Return the available schedule for date
     * Check availability dynamically based on a date
     * @param Carbon $date
     * @return null | array.
     */
    public function getWorkingHoursForDate(Carbon $date)
    {
        // use array_filter without callback to remove empty or equivalent values from array
        $hours = array_filter([
            // dynamic access the column using the date param
            $this->{strtolower($date->format('l')) . '_starts_at'},
            $this->{strtolower($date->format('l')) . '_ends_at'},
        ]);

        return empty($hours) ? null : $hours;
    }
}
