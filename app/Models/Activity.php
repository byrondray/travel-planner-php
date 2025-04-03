<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'itinerary_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'cost',
        'type',
        'position'
    ];
}