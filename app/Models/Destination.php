<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_plan_id',
        'name',
        'description',
        'country',
        'city',
        'arrival_date',
        'departure_date',
        'position'
    ];
}
