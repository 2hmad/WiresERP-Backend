<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferSafes extends Model
{
    use HasFactory;
    public $table = 'transfer_safes';
    protected $fillable = ['company_id', 'from_safe', 'to_safe', 'amount', 'notes'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
