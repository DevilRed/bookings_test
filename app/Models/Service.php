<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Service extends Model
{
    use HasFactory;

    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    // custom accesors for money formatting
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn (int $price) => '$' . number_format($price / 100, 2, '.', ' '),
        );
    }
}
