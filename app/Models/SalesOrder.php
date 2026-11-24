<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_id',
        'visit_id',
        'visit_task_id',
        'order_date',
        'status',
        'payment_method',
        'payment_status',
        'total_amount',
        'down_payment',
        'paid_amount',
        'remaining_amount',
        'due_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'due_date' => 'date',
            'total_amount' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function visitTask(): BelongsTo
    {
        return $this->belongsTo(VisitTask::class, 'visit_task_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class)->latest('payment_date');
    }

    /**
     * Determine if order has overdue tempo receivable
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->remaining_amount <= 0 || ! $this->due_date) {
            return false;
        }

        return $this->due_date->lt(Carbon::today());
    }

    /**
     * Get days remaining until due date, or days overdue
     */
    public function getDaysOverdueAttribute(): int
    {
        if (! $this->is_overdue || ! $this->due_date) {
            return 0;
        }

        return (int) $this->due_date->diffInDays(Carbon::today());
    }

    public function getDaysRemainingAttribute(): int
    {
        if (! $this->due_date || $this->remaining_amount <= 0) {
            return 0;
        }

        if ($this->due_date->lt(Carbon::today())) {
            return 0;
        }

        return (int) Carbon::today()->diffInDays($this->due_date);
    }

    /**
     * Refresh payment amounts and status after payment addition
     */
    public function recalculatePaymentStatus(): void
    {
        $totalPaid = (float) $this->payments()->sum('amount') + (float) $this->down_payment;
        $this->paid_amount = $totalPaid;
        $this->remaining_amount = max(0, (float) $this->total_amount - $totalPaid);

        if ($this->remaining_amount <= 0) {
            $this->payment_status = 'lunas';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = $this->is_overdue ? 'jatuh_tempo' : 'sebagian';
        } else {
            $this->payment_status = $this->is_overdue ? 'jatuh_tempo' : 'belum_lunas';
        }

        $this->save();
    }
}
