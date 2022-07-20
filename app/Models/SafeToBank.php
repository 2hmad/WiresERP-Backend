<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafeToBank extends Model
{
    use HasFactory;
    public $table = "safe_bank_transfer";
    protected $fillable = [
        'company_id',
        'user_id',
        'from_safe',
        'to_bank',
        'amount',
        'notes',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
    public function bank()
    {
        return $this->belongsTo(Banks::class, 'to_bank');
    }
    public function safe()
    {
        return $this->belongsTo(Safes::class, 'from_safe');
    }
}
