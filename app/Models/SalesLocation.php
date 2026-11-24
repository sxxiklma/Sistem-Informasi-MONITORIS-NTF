<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_id', 'session_id', 'latitude', 'longitude',
        'accuracy', 'speed', 'heading', 'recorded_at', 'received_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'accuracy' => 'decimal:2',
            'speed' => 'decimal:2',
            'heading' => 'decimal:2',
            'recorded_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrackingSession::class, 'session_id');
    }
}
