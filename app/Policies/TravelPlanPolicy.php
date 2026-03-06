<?php

namespace App\Policies;

use App\Models\TravelPlan;
use App\Models\User;

class TravelPlanPolicy
{
    public function view(User $user, TravelPlan $travelPlan): bool
    {
        return $user->id === $travelPlan->user_id;
    }

    public function update(User $user, TravelPlan $travelPlan): bool
    {
        return $user->id === $travelPlan->user_id;
    }

    public function delete(User $user, TravelPlan $travelPlan): bool
    {
        return $user->id === $travelPlan->user_id;
    }
}
