<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'warehouse_name', 'branch_id'];
    protected $hidden = [];
    public $timestamps = false;
}
