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
        'address',
        'country',
        'founder_name',
        'business_field',
        'currency',
        'tax_number',
        'civil_registration_number',
        'tax_value_added',
        'logo',
        'company_stamp',
        'status',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function fiscal()
    {
        return $this->hasOne(Fiscals::class, 'company_id');
    }
}
