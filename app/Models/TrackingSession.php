<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrackingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_id', 'task_id', 'started_at', 'ended_at', 'status',
        'total_points', 'total_distance_meters',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'total_distance_meters' => 'decimal:2',
        ];
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(VisitTask::class, 'task_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(SalesLocation::class, 'session_id')->orderBy('recorded_at');
    }

    public function latestLocation(): HasOne
    {
        return $this->hasOne(SalesLocation::class, 'session_id')->latestOfMany('recorded_at');
    }
}
