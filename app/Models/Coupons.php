<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
    use HasFactory;
    public $table = "discount_coupons";
    protected $fillable = [
        'company_id',
        'code',
        'amount',
        'expire_date',
        'section',
        'client_id',
        'category_id',
        'product_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
