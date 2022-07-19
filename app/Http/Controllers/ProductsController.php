<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\TransferWarehouses;
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
        if ($user->role == 'manager') {
            $check = Products::where([
                ['company_id', $user->company_id],
                ['product_name', $request->product_name]
            ])->first();
            if ($check == null) {
                if ($request->hasFile('image')) {
                    $validated = $request->validate([
                        'image' => 'mimes:jpeg,jpg,png|max:1000',
                    ]);
                    if ($validated) {
                        $image = $request->file('image');
                        $imageName = $request->product_name . '.' . $image->getClientOriginalExtension();
                        $image->move(public_path('storage/products/company-' . $user->company_id), $imageName);
                        Products::create([
                            'company_id' => $user->company_id,
                            'warehouse_id' => $request->warehouse_id,
                            'barcode' => $request->barcode,
                            'warehouse_balance' => $request->warehouse_balance,
                            'total_price' => $request->total_price,
                            'product_name' => $request->product_name,
                            'product_unit' => $request->product_unit,
                            'wholesale_price' => $request->wholesale_price,
                            'piece_price' => $request->piece_price,
                            'min_stock' => $request->min_stock,
                            'product_model' => $request->product_model,
                            'category' => $request->category,
                            'sub_category' => $request->sub_category,
                            'description' => $request->description,
                            'image' => $imageName,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } else {
                    Products::create([
                        'company_id' => $user->company_id,
                        'warehouse_id' => $request->warehouse_id,
                        'barcode' => $request->barcode,
                        'warehouse_balance' => $request->warehouse_balance,
                        'total_price' => $request->total_price,
                        'product_name' => $request->product_name,
                        'product_unit' => $request->product_unit,
                        'wholesale_price' => $request->wholesale_price,
                        'piece_price' => $request->piece_price,
                        'min_stock' => $request->min_stock,
                        'product_model' => $request->product_model,
                        'category' => $request->category,
                        'sub_category' => $request->sub_category,
                        'description' => $request->description,
                        'image' => null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else {
                return response()->json(['alert_en' => 'Product already exist', 'alert_ar' => 'المنتج مضاف من قبل'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function editProduct(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $check = Products::where([
                ['company_id', $user->company_id],
                ['id', $request->product_id]
            ])->first();
            if ($check !== null) {
                Products::where('id', $request->product_id)->update([
                    'warehouse_id' => $request->warehouse_id,
                    'barcode' => $request->barcode,
                    'warehouse_balance' => $request->warehouse_balance,
                    'total_price' => $request->total_price,
                    'product_name' => $request->product_name,
                    'product_unit' => $request->product_unit,
                    'wholesale_price' => $request->wholesale_price,
                    'piece_price' => $request->piece_price,
                    'min_stock' => $request->min_stock,
                    'product_model' => $request->product_model,
                    'category' => $request->category,
                    'sub_category' => $request->sub_category,
                    'description' => $request->description,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                return response()->json(['alert_en' => 'Product not found', 'alert_ar' => 'المنتج غير موجود'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteProduct(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $check = Products::where([
                ['company_id', $user->company_id],
                ['id', $request->product_id]
            ])->first();
            if ($check !== null) {
                Products::where('id', $request->product_id)->delete();
                TransferWarehouses::where('product_id', $request->product_id)->delete();
            } else {
                return response()->json(['alert_en' => 'Product not found', 'alert_ar' => 'المنتج غير موجود'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
}
