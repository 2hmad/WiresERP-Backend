<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'country',
        'business_field',
        'currency',
        'tax_number',
        'civil_registration_number',
        'tax_value_added',
        'logo',
        'status',
        'created_at',
        'updated_at',
    ];
    protected $hidden = [];
}
