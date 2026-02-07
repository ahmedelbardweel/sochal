<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'password',
        'role', // user, creator, moderator, admin
        'status', // active, suspended, banned
        'display_name',
        'bio',
        'avatar_url',
        'cover_url',
        'website',
        'location',
        'birthday',
        'gender',
        'is_private',
        'show_activity_status',
        'allow_tags',
        'allow_comments',
        'allow_messages',
        'last_active_at',
        'is_verified',
        'metadata',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'suspended_until' => 'datetime',
        'last_active_at' => 'datetime',
        'is_private' => 'boolean',
        'show_activity_status' => 'boolean',
        'password' => 'hashed',
        'is_verified' => 'boolean',
        'metadata' => 'array',
    ];

    // Relationships will be added as we create related models
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LiveSession::class, 'host_id');
    }

    public function liveParticipations(): HasMany
    {
        return $this->hasMany(LiveParticipant::class, 'user_id');
    }

    public function liveComments(): HasMany
    {
        return $this->hasMany(LiveComment::class, 'user_id');
    }

    public function liveBans(): HasMany
    {
        return $this->hasMany(LiveBan::class, 'user_id');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'user_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function isFollowing(User $user)
    {
        return $this->following()
            ->where('following_id', $user->id)
            ->wherePivot('status', 'accepted')
            ->exists();
    }

    public function hasPendingFollowRequest(User $user)
    {
        return $this->following()
            ->where('following_id', $user->id)
            ->wherePivot('status', 'pending')
            ->exists();
    }

    /**
     * Normalize image URLs
     */
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_online',
        'last_active_human',
    ];

    /**
     * Check if user is online
     */
    public function getIsOnlineAttribute()
    {
        if (!$this->last_active_at) return false;
        // Consider online if active in last 3 minutes (Hybrid Fallback)
        return $this->last_active_at->diffInMinutes(now()) < 3;
    }

    /**
     * Get human readable last active
     */
    public function getLastActiveHumanAttribute()
    {
        if (!$this->last_active_at) return 'Never';
        return $this->last_active_at->diffForHumans();
    }

    public function getAvatarUrlAttribute($value)
    {
        if (empty($value)) return $value;
        if (str_starts_with($value, 'http')) {
            $path = parse_url($value, PHP_URL_PATH);
            if ($path && str_contains($path, '/storage/')) {
                return asset(Str::after($path, '/storage/'));
            }
            return $value;
        }
        return asset('storage/' . $value);
    }

    public function getCoverUrlAttribute($value)
    {
        if (empty($value)) return $value;
        if (str_starts_with($value, 'http')) {
            $path = parse_url($value, PHP_URL_PATH);
            if ($path && str_contains($path, '/storage/')) {
                return asset(Str::after($path, '/storage/'));
            }
            return $value;
        }
        return asset('storage/' . $value);
    }
}
