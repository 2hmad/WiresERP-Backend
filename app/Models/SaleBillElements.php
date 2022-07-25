<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleBillElements extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'sale_bill_id',
        'product_id',
        'product_price',
        'quantity',
        'unit',
        'quantity_price',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
