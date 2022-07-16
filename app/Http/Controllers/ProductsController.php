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
    public function addProduct(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $dataDecode = json_decode($request->data, true);
        $check = Products::where([
            ['company_id', $user->company_id],
            ['product_name', $dataDecode['product_name']]
        ])->first();
        if ($check == null) {
            if ($request->hasFile('image')) {
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,jpg,png|max:1000',
                ]);
                if ($validated) {
                    $image = $request->file('image');
                    $imageName = $dataDecode['product_name'] . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('products/company-' . $user->company_id), $imageName);
                    Products::create([
                        'company_id' => $user->company_id,
                        'warehouse_id' => $dataDecode['warehouse_id'],
                        'barcode' => $dataDecode['barcode'],
                        'warehouse_balance' => $dataDecode['warehouse_balance'],
                        'total_price' => $dataDecode['total_price'],
                        'product_name' => $dataDecode['product_name'],
                        'product_unit' => $dataDecode['product_unit'],
                        'wholesale_price' => $dataDecode['wholesale_price'],
                        'piece_price' => $dataDecode['piece_price'],
                        'min_stock' => $dataDecode['min_stock'],
                        'product_model' => $dataDecode['product_model'],
                        'category' => $dataDecode['category'],
                        'sub_category' => $dataDecode['sub_category'],
                        'description' => $dataDecode['description'],
                        'image' => $imageName,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else {
                Products::create([
                    'company_id' => $user->company_id,
                    'warehouse_id' => $dataDecode['warehouse_id'],
                    'barcode' => $dataDecode['barcode'],
                    'warehouse_balance' => $dataDecode['warehouse_balance'],
                    'total_price' => $dataDecode['total_price'],
                    'product_name' => $dataDecode['product_name'],
                    'product_unit' => $dataDecode['product_unit'],
                    'wholesale_price' => $dataDecode['wholesale_price'],
                    'piece_price' => $dataDecode['piece_price'],
                    'min_stock' => $dataDecode['min_stock'],
                    'product_model' => $dataDecode['product_model'],
                    'category' => $dataDecode['category'],
                    'sub_category' => $dataDecode['sub_category'],
                    'description' => $dataDecode['description'],
                    'image' => "product-placeholder.png",
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        } else {
            return response()->json(['alert_en' => 'Product already exist', 'alert_ar' => 'المنتج مضاف من قبل'], 404);
        }
    }
}
