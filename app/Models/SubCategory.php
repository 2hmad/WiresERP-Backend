<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    public $table = 'sub_categories';
    protected $fillable = ['company_id', 'category_id', 'sub_category_name'];
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
