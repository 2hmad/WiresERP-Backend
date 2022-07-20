<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanksTransfer extends Model
{
    use HasFactory;
    public $table = "banks_transfer";
    protected $fillable = [
        'company_id',
        'user_id',
        'from_bank',
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
    public function f_bank()
    {
        return $this->belongsTo(Banks::class, 'from_bank');
    }
    public function t_bank()
    {
        return $this->belongsTo(Banks::class, 'to_bank');
    }
}
