<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'badge_color',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Relationship to Customer by matching type code
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'type', 'code');
    }

    /**
     * Badge CSS classes helper
     */
    public function getBadgeClassAttribute(): string
    {
        return match ($this->badge_color) {
            'sky' => 'bg-sky-50 text-sky-700 border-sky-200',
            'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
            'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'rose' => 'bg-rose-50 text-rose-700 border-rose-200',
            'slate' => 'bg-slate-100 text-slate-700 border-slate-200',
            default => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }
}
