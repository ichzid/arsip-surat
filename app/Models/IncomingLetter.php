<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class IncomingLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'agenda_number',
        'mail_number',
        'mail_date',
        'received_date',
        'origin',
        'subject',
        'file_path',
        'status',
        'created_by',
    ];

    protected $casts = [
        'mail_date' => 'date',
        'received_date' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class);
    }

    /**
     * Scope the query to only include letters visible to a specific user.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        // Admin, Pimpinan, and Divisi Umum can see all
        // We check Role or Division Code. Let's use Role for robustness if available, 
        // or check Division Code 'UMUM'.
        
        $isPrivileged = $user->hasRole(['Admin', 'Pimpinan', 'Operator Divisi Umum']) 
                        || ($user->division && $user->division->code === 'UMUM');

        if ($isPrivileged) {
            return $query;
        }

        // Regular Division Operators only see letters disposed to them
        return $query->whereHas('dispositions', function ($q) use ($user) {
            $q->where('to_division_id', $user->division_id);
        });
    }
}
