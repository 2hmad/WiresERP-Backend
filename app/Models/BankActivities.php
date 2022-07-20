<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankActivities extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'user_id',
        'bank_id',
        'amount',
        'type',
        'notes',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
