<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ClientRequestHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_request_id',
        'actor_id',
        'entry_type',
        'old_status',
        'new_status',
        'internal_note',
        'client_note',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(ClientRequest::class, 'client_request_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
