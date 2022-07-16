<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        's_name',
        'indebt_type',
        'indebt_amount',
        's_phone',
        's_address',
        's_notes',
        'deal_type',
        's_email',
        's_company',
        's_nationality',
        's_tax_number',
        'created_at',
        'updated_at'
    ];
    protected $hidden = ['created_at', 'updated_at'];
}
