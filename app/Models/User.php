<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\DatabaseNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo_profil',
        'role',
        'facebook_id',
        'google_id',
        'voice_id',
        'voice_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function isAuteur()
    {
        return $this->role === 'auteur';
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function authorSubscriptions()
    {
        return $this->hasMany(AuthorSubscription::class);
    }

    public function hasActiveSubscription()
    {
        return $this->authorSubscriptions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function currentSubscription()
    {
        return $this->authorSubscriptions()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->with('subscription')
            ->first();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptionPayments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }
}
