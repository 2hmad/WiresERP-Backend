<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'c_name',
        'releated_user',
        'indebt_type',
        'indebt_amount',
        'c_phone',
        'c_address',
        'c_notes',
        'deal_type',
        'c_email',
        'c_company',
        'c_nationality',
        'c_tax_number',
        'created_at',
        'updated_at'
    ];
    protected $hidden = ['created_at', 'updated_at'];
}
