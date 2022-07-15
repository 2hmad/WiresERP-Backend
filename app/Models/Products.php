<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id', 'warehouse_id', 'barcode', 'warehouse_balance', 'total_price', 'product_name', 'product_unit', 'wholesale_price', 'piece_price', 'min_stock', 'product_model', 'category', 'sub_category', 'description', 'image', 'created_at', 'updated_at'
    ];
    protected $hidden = [];
}
