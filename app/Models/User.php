<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'organization',
    'email',
    'password',
    'role',
    'is_active',
    'google_id',
    'google_profile_completed_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'google_profile_completed_at' => 'datetime',
        ];
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function submittedRequests(): HasMany
    {
        return $this->hasMany(ClientRequest::class, 'submitted_by');
    }

    public function uploadedDeliverables(): HasMany
    {
        return $this->hasMany(Deliverable::class, 'uploaded_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }

    public function createdBillings(): HasMany
    {
        return $this->hasMany(BillingRecord::class, 'created_by');
    }

    public function requestHistoryEntries(): HasMany
    {
        return $this->hasMany(ClientRequestHistory::class, 'actor_id');
    }
}
