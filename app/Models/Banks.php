<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banks extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'bank_name', 'bank_balance'];
    protected $hidden = [];
    public $timestamps = false;
}
