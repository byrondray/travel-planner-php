<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_plan_id',
        'destination_id',
        'title',
        'date',
        'description',
        'transportation',
        'accommodations',
        'meals',
        'position'
    ];

    protected $casts = [
        'transportation' => 'array',
        'accommodations' => 'array',
        'meals' => 'array',
        'date' => 'date',
    ];

    public function travelPlan()
    {
        return $this->belongsTo(TravelPlan::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}