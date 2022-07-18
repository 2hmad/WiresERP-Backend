<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferWarehouses extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'from_warehouse',
        'to_warehouse',
        'product_id',
        'quantity',
        'date',
        'notes',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function f_warehouse()
    {
        return $this->belongsTo(Warehouses::class, 'from_warehouse');
    }
    public function t_warehouse()
    {
        return $this->belongsTo(Warehouses::class, 'to_warehouse');
    }
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
