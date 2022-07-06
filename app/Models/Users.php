<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'role',
        'token',
        'company_id',
        'branch_id',
        'status',
    ];
    protected $hidden = [
        'password'
    ];
    public $timestamps = false;
}
