<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branches extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'branch_name',
        'branch_phone',
        'branch_address',
        'commercial_registration_number',
    ];
    protected $hidden = [];
    public $timestamps = false;
}
