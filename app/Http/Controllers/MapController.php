<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    public function visits(): JsonResponse
    {
        $visits = Visit::with('customer')->where(function ($query) {
            $query->whereNotNull('check_in_latitude')->orWhereHas('customer', fn ($customer) => $customer->whereNotNull('latitude')->whereNotNull('longitude'));
        })->latest()->limit(200)->get();

        return response()->json($visits->map(fn (Visit $visit) => [
            'id' => $visit->id,
            'name' => $visit->customer->name,
            'status' => $visit->status,
            'latitude' => (float) ($visit->check_in_latitude ?? $visit->customer->latitude),
            'longitude' => (float) ($visit->check_in_longitude ?? $visit->customer->longitude),
            'date' => $visit->getRawOriginal('planned_date'),
            'distance_meters' => $visit->distance_from_home_meters,
        ]));
    }
}
