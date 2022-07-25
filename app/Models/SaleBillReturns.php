<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleBillReturns extends Model
{
    use HasFactory;
    public $table = "sale_bill_return";
    protected $fillable = [
        'company_id',
        'sale_bill_id',
        'product_id',
        'client_id',
        'quantity',
        'date_time',
        'notes',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
