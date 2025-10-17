<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Tracking sessions relationship
     */
    public function trackingSessions()
    {
        return $this->hasMany(TrackingSession::class);
    }

    /**
     * Location shares where user is sharing their location
     */
    public function locationShares()
    {
        return $this->hasMany(LocationShare::class);
    }

    /**
     * Location shares where user receives shared locations
     */
    public function receivedLocationShares()
    {
        return $this->hasMany(LocationShare::class, 'shared_with_user_id');
    }

    /**
     * Geofences relationship
     */
    public function geofences()
    {
        return $this->hasMany(Geofence::class);
    }

    /**
     * Trip statistics relationship
     */
    public function tripStatistics()
    {
        return $this->hasMany(TripStatistic::class);
    }

    /**
     * Location analytics relationship
     */
    public function locationAnalytics()
    {
        return $this->hasMany(LocationAnalytic::class);
    }
}
