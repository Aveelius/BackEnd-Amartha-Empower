<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OjkReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'female_borrowers',
        'male_borrowers',
        'active_loans',
        'total_disbursed',
        'total_outstanding',
    ];

    protected $casts = [
        'report_date' => 'date',
        'total_disbursed' => 'decimal:2',
        'total_outstanding' => 'decimal:2',
    ];
}
