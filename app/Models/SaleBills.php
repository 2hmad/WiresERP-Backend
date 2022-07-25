<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleBills extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'client_id',
        'bill_number',
        'date_time',
        'warehouse_id',
        'value_added_tax',
        'final_total',
        'paid',
        'status',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
