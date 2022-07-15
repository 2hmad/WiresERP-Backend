<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Users;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function products(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        return Products::where('company_id', $user->company_id)->get();
    }
}
