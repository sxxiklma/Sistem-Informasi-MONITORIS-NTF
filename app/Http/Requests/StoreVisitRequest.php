<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('kelola kunjungan') ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'planned_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'], 'pic_name' => ['nullable', 'string', 'max:255'], 'pic_phone' => ['nullable', 'string', 'max:30'], 'visit_order' => ['nullable', 'integer', 'min:1'], 'purpose' => ['nullable', 'string', 'max:255'], 'status' => ['nullable', 'in:planned,in_progress,completed,cancelled'], 'home_latitude' => ['nullable', 'numeric', 'between:-90,90'], 'home_longitude' => ['nullable', 'numeric', 'between:-180,180'], 'distance_from_home_meters' => ['nullable', 'integer', 'min:0'], 'route_distance_meters' => ['nullable', 'integer', 'min:0'], 'route_duration_seconds' => ['nullable', 'integer', 'min:0'], 'route_geometry' => ['nullable', 'json'],
        ];
    }
}
