<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use HasFactory;

    protected $table = 'app_notifications';

    protected $fillable = [
        'recipient_id', 'sender_id', 'type', 'title', 'message',
        'entity_type', 'entity_id', 'action_url', 'data', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead(): void
    {
        if (! $this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Static helper to create a notification
    public static function send(
        int $recipientId,
        string $type,
        string $title,
        string $message,
        ?int $senderId = null,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $actionUrl = null,
        array $data = []
    ): self {
        return self::create([
            'recipient_id' => $recipientId,
            'sender_id' => $senderId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action_url' => $actionUrl,
            'data' => $data ?: null,
        ]);
    }
}
