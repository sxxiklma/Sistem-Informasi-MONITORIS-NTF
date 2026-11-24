<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisitTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_code', 'sales_id', 'supervisor_id', 'customer_id', 'created_by',
        'scheduled_at', 'due_at', 'purpose', 'target_result', 'instructions',
        'priority', 'status',
        'responded_at',
        'check_in_at', 'check_in_latitude', 'check_in_longitude', 'check_in_distance_meters',
        'check_out_at', 'check_out_latitude', 'check_out_longitude',
        'visit_result', 'visit_notes', 'documentation_image',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'due_at' => 'datetime',
            'responded_at' => 'datetime',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'check_in_latitude' => 'decimal:7',
            'check_in_longitude' => 'decimal:7',
            'check_out_latitude' => 'decimal:7',
            'check_out_longitude' => 'decimal:7',
            'check_in_distance_meters' => 'decimal:2',
        ];
    }

    // Valid status transitions
    const STATUS_TRANSITIONS = [
        'MENUNGGU_RESPONS' => ['DITERIMA', 'DITOLAK', 'DIBATALKAN'],
        'DITERIMA' => ['DALAM_PERJALANAN', 'DIBATALKAN'],
        'DITOLAK' => ['DIJADWALKAN_ULANG', 'DIBATALKAN'],
        'DIJADWALKAN_ULANG' => ['MENUNGGU_RESPONS', 'DIBATALKAN'],
        'DALAM_PERJALANAN' => ['TIBA_DI_LOKASI', 'DIBATALKAN'],
        'TIBA_DI_LOKASI' => ['SEDANG_KUNJUNGAN', 'SELESAI', 'DALAM_PERJALANAN', 'DIBATALKAN'],
        'SEDANG_KUNJUNGAN' => ['SELESAI', 'DIBATALKAN'],
        'SELESAI' => [],
        'DIBATALKAN' => [],
    ];

    const PRIORITY_COLORS = [
        'rendah' => 'slate',
        'normal' => 'blue',
        'tinggi' => 'amber',
        'mendesak' => 'rose',
    ];

    const STATUS_COLORS = [
        'MENUNGGU_RESPONS' => 'amber',
        'DITERIMA' => 'blue',
        'DITOLAK' => 'rose',
        'DIJADWALKAN_ULANG' => 'violet',
        'DALAM_PERJALANAN' => 'cyan',
        'TIBA_DI_LOKASI' => 'indigo',
        'SEDANG_KUNJUNGAN' => 'orange',
        'SELESAI' => 'emerald',
        'DIBATALKAN' => 'slate',
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::STATUS_TRANSITIONS[$this->status] ?? []);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(VisitTaskResponse::class, 'task_id');
    }

    public function latestResponse(): HasOne
    {
        return $this->hasOne(VisitTaskResponse::class, 'task_id')->latestOfMany();
    }

    public function histories(): HasMany
    {
        return $this->hasMany(VisitTaskHistory::class, 'task_id')->orderBy('changed_at');
    }

    public function trackingSession(): HasOne
    {
        return $this->hasOne(TrackingSession::class, 'task_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'visit_task_id');
    }

    public function order(): HasOne
    {
        return $this->hasOne(SalesOrder::class, 'visit_task_id')->latestOfMany();
    }

    public function getDocumentationImageUrlAttribute(): ?string
    {
        if (! $this->documentation_image) {
            return null;
        }

        return asset('storage/'.$this->documentation_image);
    }

    public static function generateCode(): string
    {
        $year = now()->format('Y');
        $maxTask = self::where('task_code', 'like', "TASK-{$year}-%")->orderByDesc('id')->first();
        $seq = $maxTask ? (intval(substr($maxTask->task_code, -5)) + 1) : 1;
        do {
            $code = 'TASK-'.$year.'-'.str_pad($seq, 5, '0', STR_PAD_LEFT);
            $seq++;
        } while (self::where('task_code', $code)->exists());

        return $code;
    }

    /**
     * Get structured journey, tracking, navigation, and duration payload for map visualization.
     */
    public function getJourneyPayload(): array
    {
        $destLat = $this->customer?->latitude ? floatval($this->customer->latitude) : null;
        $destLng = $this->customer?->longitude ? floatval($this->customer->longitude) : null;

        $checkInLat = $this->check_in_latitude ? floatval($this->check_in_latitude) : null;
        $checkInLng = $this->check_in_longitude ? floatval($this->check_in_longitude) : null;

        $checkOutLat = $this->check_out_latitude ? floatval($this->check_out_latitude) : null;
        $checkOutLng = $this->check_out_longitude ? floatval($this->check_out_longitude) : null;

        // Session locations if available
        $session = $this->trackingSession ?? TrackingSession::where('task_id', $this->id)->orWhere(function ($q) {
            $q->where('sales_id', $this->sales_id)
                ->whereDate('started_at', $this->scheduled_at ?? now());
        })->latest('id')->first();

        $points = [];
        if ($session) {
            $points = $session->locations()->orderBy('recorded_at')->get()->map(fn ($loc) => [
                'lat' => floatval($loc->latitude),
                'lng' => floatval($loc->longitude),
                'time' => $loc->recorded_at?->format('H:i:s'),
                'speed' => $loc->speed ? round(floatval($loc->speed), 1) : null,
            ])->toArray();
        }

        // Determine Origin (Titik Awal Berangkat)
        $originLat = null;
        $originLng = null;
        $originLabel = 'Titik Awal Berangkat';
        $originTime = null;

        if (! empty($points)) {
            $originLat = $points[0]['lat'];
            $originLng = $points[0]['lng'];
            $originTime = $points[0]['time'];
        } elseif ($this->status === 'SELESAI') {
            $tripHistory = $this->histories->where('to_status', 'DALAM_PERJALANAN')->first();
            if ($tripHistory && ! empty($tripHistory->metadata['latitude'])) {
                $originLat = floatval($tripHistory->metadata['latitude']);
                $originLng = floatval($tripHistory->metadata['longitude']);
                $originTime = $tripHistory->changed_at?->format('H:i');
            } elseif ($checkOutLat && $checkInLat && (abs($checkOutLat - $checkInLat) > 0.0005 || abs($checkOutLng - $checkInLng) > 0.0005)) {
                $originLat = $checkOutLat;
                $originLng = $checkOutLng;
                $originTime = $this->responded_at?->format('H:i') ?? $this->scheduled_at?->format('H:i');
            } elseif ($checkInLat) {
                // Approximate starting point along the corridor
                $originLat = $checkInLat + 0.012;
                $originLng = $checkInLng - 0.008;
                $originTime = $this->scheduled_at?->format('H:i');
            }
        } elseif (in_array($this->status, ['DALAM_PERJALANAN', 'TIBA_DI_LOKASI', 'SEDANG_KUNJUNGAN'])) {
            $latestLoc = $this->sales?->activeTrackingSession?->latestLocation;
            if ($latestLoc) {
                $originLat = floatval($latestLoc->latitude);
                $originLng = floatval($latestLoc->longitude);
                $originLabel = 'Posisi Sales Terkini (Live GPS)';
                $originTime = $latestLoc->recorded_at?->format('H:i');
            } elseif ($checkInLat) {
                $originLat = $checkInLat;
                $originLng = $checkInLng;
                $originLabel = 'Lokasi Check-in Outlet';
                $originTime = $this->check_in_at?->format('H:i');
            }
        } else {
            // MENUNGGU_RESPONS, DITERIMA, etc. (Kadiv Planning Mode)
            $latestLoc = $this->sales?->activeTrackingSession?->latestLocation;
            if ($latestLoc) {
                $originLat = floatval($latestLoc->latitude);
                $originLng = floatval($latestLoc->longitude);
                $originLabel = 'Lokasi Terkini Sales (Live GPS)';
                $originTime = $latestLoc->recorded_at?->format('H:i');
            } elseif ($destLat && $destLng) {
                $originLat = $destLat > -6.8 ? -6.2297 : -7.3544;
                $originLng = $destLng > 110 ? 112.6778 : 106.8450;
                $originLabel = $destLat > -6.8 ? 'Kantor Cabang Jakarta' : 'Pangkalan / Gudang Sidoarjo';
            }
        }

        // Calculate Durations
        $tripDurationMinutes = null;
        $visitDurationMinutes = null;
        $totalDurationMinutes = null;

        $startTime = $session?->started_at ?? $this->responded_at ?? $this->scheduled_at;
        $checkInTime = $this->check_in_at;
        $endTime = $this->check_out_at ?? $session?->ended_at;

        if ($startTime && $checkInTime) {
            $tripDurationMinutes = intval(round(max(1, abs($startTime->diffInSeconds($checkInTime)) / 60)));
        }

        if ($checkInTime && $endTime) {
            $visitDurationMinutes = intval(round(max(1, abs($checkInTime->diffInSeconds($endTime)) / 60)));
        }

        if ($startTime && $endTime) {
            $totalDurationMinutes = intval(round(max(1, abs($startTime->diffInSeconds($endTime)) / 60)));
        } elseif ($startTime && in_array($this->status, ['DALAM_PERJALANAN', 'TIBA_DI_LOKASI', 'SEDANG_KUNJUNGAN'])) {
            $totalDurationMinutes = intval(round(max(1, abs($startTime->diffInSeconds(now())) / 60)));
        }

        // Distance in KM
        $distanceKm = null;
        if ($session && $session->total_distance_meters > 0) {
            $distanceKm = round($session->total_distance_meters / 1000, 2);
        } elseif ($originLat && $originLng && $destLat && $destLng) {
            $earthRadius = 6371; // km
            $dLat = deg2rad($destLat - $originLat);
            $dLon = deg2rad($destLng - $originLng);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($originLat)) * cos(deg2rad($destLat)) *
                 sin($dLon / 2) * sin($dLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distanceKm = round($earthRadius * $c * 1.25, 1); // 1.25 road winding factor
        }

        return [
            'id' => $this->id,
            'task_code' => $this->task_code,
            'status' => $this->status,
            'priority' => $this->priority,
            'sales' => [
                'id' => $this->sales_id,
                'name' => $this->sales?->name ?? 'Sales Motoris',
                'team' => $this->sales?->team?->name ?? 'Tanpa Tim',
            ],
            'customer' => [
                'id' => $this->customer_id,
                'name' => $this->customer?->name ?? 'Outlet',
                'address' => $this->customer?->address ?? '-',
                'phone' => $this->customer?->phone ?? '-',
                'latitude' => $destLat,
                'longitude' => $destLng,
            ],
            'origin' => [
                'label' => $originLabel,
                'latitude' => $originLat,
                'longitude' => $originLng,
                'time' => $originTime,
            ],
            'check_in' => [
                'latitude' => $checkInLat,
                'longitude' => $checkInLng,
                'at' => $this->check_in_at?->format('d/m/Y H:i:s'),
                'time' => $this->check_in_at?->format('H:i'),
                'distance_meters' => $this->check_in_distance_meters ? round(floatval($this->check_in_distance_meters), 1) : null,
            ],
            'check_out' => [
                'latitude' => $checkOutLat,
                'longitude' => $checkOutLng,
                'at' => $this->check_out_at?->format('d/m/Y H:i:s'),
                'time' => $this->check_out_at?->format('H:i'),
                'result' => $this->visit_result,
                'notes' => $this->visit_notes,
                'documentation_image' => $this->documentation_image_url,
            ],
            'duration' => [
                'trip_minutes' => $tripDurationMinutes,
                'trip_text' => $tripDurationMinutes ? ($tripDurationMinutes >= 60 ? floor($tripDurationMinutes / 60).' jam '.($tripDurationMinutes % 60).' mnt' : $tripDurationMinutes.' Menit') : null,
                'visit_minutes' => $visitDurationMinutes,
                'visit_text' => $visitDurationMinutes ? ($visitDurationMinutes >= 60 ? floor($visitDurationMinutes / 60).' jam '.($visitDurationMinutes % 60).' mnt' : $visitDurationMinutes.' Menit') : null,
                'total_minutes' => $totalDurationMinutes,
                'total_text' => $totalDurationMinutes ? ($totalDurationMinutes >= 60 ? floor($totalDurationMinutes / 60).' jam '.($totalDurationMinutes % 60).' mnt' : $totalDurationMinutes.' Menit') : null,
                'started_at_text' => $startTime?->format('H:i').' WIB',
                'ended_at_text' => $endTime ? ($endTime->format('H:i').' WIB') : ($this->status === 'SELESAI' ? 'Selesai' : 'Sedang Berjalan'),
            ],
            'distance_km' => $distanceKm,
            'breadcrumbs' => $points,
        ];
    }
}
