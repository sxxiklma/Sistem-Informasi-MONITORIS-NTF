<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'supervisor_id', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public static function findOrCreateByName(?string $name): ?self
    {
        $name = trim($name ?? '');
        if ($name === '') {
            return null;
        }

        $existing = self::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
        if ($existing) {
            return $existing;
        }

        $clean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $baseCode = 'TM-'.substr($clean ?: 'TIM', 0, 15);
        $code = $baseCode;
        $counter = 1;
        while (self::where('code', $code)->exists()) {
            $code = substr($baseCode, 0, 25).'-'.$counter;
            $counter++;
        }

        return self::create([
            'name' => $name,
            'code' => $code,
            'is_active' => true,
        ]);
    }
}
