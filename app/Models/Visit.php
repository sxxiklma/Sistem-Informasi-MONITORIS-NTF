<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'customer_id', 'planned_date', 'status', 'check_in_at', 'check_out_at', 'check_in_latitude', 'check_in_longitude', 'check_out_latitude', 'check_out_longitude', 'home_latitude', 'home_longitude', 'distance_from_home_meters', 'route_distance_meters', 'route_duration_seconds', 'route_geometry', 'pic_name', 'pic_phone', 'visit_order', 'purpose', 'notes', 'outcome'];

    protected function casts(): array
    {
        return ['planned_date' => 'date', 'check_in_at' => 'datetime', 'check_out_at' => 'datetime', 'check_in_latitude' => 'decimal:7', 'check_in_longitude' => 'decimal:7', 'check_out_latitude' => 'decimal:7', 'check_out_longitude' => 'decimal:7', 'home_latitude' => 'decimal:7', 'home_longitude' => 'decimal:7', 'distance_from_home_meters' => 'integer', 'route_distance_meters' => 'integer', 'route_duration_seconds' => 'integer', 'visit_order' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
