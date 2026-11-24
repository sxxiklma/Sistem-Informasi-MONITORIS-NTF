<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'team_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function salesTargets()
    {
        return $this->hasMany(SalesTarget::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Visit Tasks (as sales)
    public function visitTasks()
    {
        return $this->hasMany(VisitTask::class, 'sales_id');
    }

    // Visit Tasks supervised (as supervisor)
    public function supervisedTasks()
    {
        return $this->hasMany(VisitTask::class, 'supervisor_id');
    }

    // GPS Tracking sessions
    public function trackingSessions()
    {
        return $this->hasMany(TrackingSession::class, 'sales_id');
    }

    public function activeTrackingSession()
    {
        return $this->hasOne(TrackingSession::class, 'sales_id')->where('status', 'active');
    }

    // In-app notifications
    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class, 'recipient_id')->latest();
    }

    public function unreadNotificationsCount(): int
    {
        return $this->appNotifications()->unread()->count();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/'.$this->avatar);
        }

        return null;
    }

    // Supervised team members (for Supervisor role)
    public function teamMembers()
    {
        if (! $this->team_id) {
            return User::whereNull('id'); // empty query
        }

        return User::where('team_id', $this->team_id)->where('id', '!=', $this->id);
    }
}
