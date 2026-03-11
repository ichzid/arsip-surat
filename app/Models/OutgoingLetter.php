<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class OutgoingLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'mail_number',
        'recipient',
        'mail_date',
        'subject',
        'division_id',
        'file_path',
        'created_by',
    ];

    protected $casts = [
        'mail_date' => 'date',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $isPrivileged = $user->hasRole(['Admin', 'Pimpinan']);

        if ($isPrivileged) {
            return $query;
        }

        return $query->where('division_id', $user->division_id);
    }
}
