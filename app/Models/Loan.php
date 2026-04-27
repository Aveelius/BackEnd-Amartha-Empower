<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_code',
        'amount',
        'tenor_months',
        'interest_rate',
        'status',
        'submitted_at',
        'approved_at',
        'disbursed_at',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'submitted_at' => 'date',
        'approved_at' => 'date',
        'disbursed_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LoanDocument::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }
}
