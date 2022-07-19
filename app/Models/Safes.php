<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Safes extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'safe_name', 'branch_id', 'safe_balance', 'safe_type'];
    protected $hidden = [];
    public $timestamps = false;
}
