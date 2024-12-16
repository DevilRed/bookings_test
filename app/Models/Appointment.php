<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected function casts():array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected $guarded = [];

    /** hook into enloquent event
     * while appointment is being created, automatically generate uuid
     *
     */
    public static function booted()
    {
        static::creating(function (Appointment $appointment) {
            $appointment->uuid = str()->uuid();
        });
    }

    public function cancelled()
    {
        return !is_null($this->cancelled_at);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
