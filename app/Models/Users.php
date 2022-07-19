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
        'image',
    ];
    protected $hidden = [
        'password'
    ];
    public $timestamps = false;

    public function branch()
    {
        return $this->hasOne(Branches::class, 'id', 'branch_id');
    }
}
