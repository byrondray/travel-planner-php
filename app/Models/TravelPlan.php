<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'budget',
        'currency',
        'status',
        'processing_status',
        'processing_error',
        'processing_started_at',
        'processing_completed_at',
        'preferences',
        'ai_prompt_used',
        'ai_response'
    ];

    protected $casts = [
        'preferences' => 'array',
        'ai_response' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'processing_started_at' => 'datetime',
        'processing_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }
}