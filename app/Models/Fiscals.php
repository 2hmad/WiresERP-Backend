<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fiscals extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'fiscal_year',
        'start_date',
        'end_date',
    ];
    protected $hidden = [];
    public $timestamps = false;
}
