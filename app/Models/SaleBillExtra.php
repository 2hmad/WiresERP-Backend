<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleBillExtra extends Model
{
    use HasFactory;
    public $table = "sale_bill_extra";
    protected $fillable = [
        'company_id',
        'sale_bill_id',
        'action',
        'action_type',
        'value',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
