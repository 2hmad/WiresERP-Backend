<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankToSafe extends Model
{
    use HasFactory;
    public $table = "bank_safe_transfer";
    protected $fillable = [
        'company_id',
        'user_id',
        'from_bank',
        'to_safe',
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
        return $this->belongsTo(Banks::class, 'from_bank');
    }
    public function safe()
    {
        return $this->belongsTo(Safes::class, 'to_safe');
    }
}
