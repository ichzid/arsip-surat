<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_letter_id',
        'from_division_id',
        'to_division_id',
        'notes',
        'instruction',
        'due_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function letter(): BelongsTo
    {
        return $this->belongsTo(IncomingLetter::class, 'incoming_letter_id');
    }

    public function fromDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'from_division_id');
    }

    public function toDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'to_division_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
