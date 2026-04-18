<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Deliverable extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'project_id',
        'uploaded_by',
        'title',
        'description',
        'file_name',
        'file_type',
        'storage_path',
        'file_url',
        'version',
        'status',
        'is_visible_to_client',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_visible_to_client' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
