<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\TransferWarehouses;
use App\Models\Users;
use App\Models\Warehouses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehousesController extends Controller
{
    public function warehouses(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        return Warehouses::where('company_id', $user->company_id)->orderBy('id', 'DESC')->get();
    }
    public function addWarehouse(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            Warehouses::create([
                'company_id' => $user->company_id,
                'warehouse_name' => $request->warehouse_name,
                'branch_id' => $request->branch_id,
            ]);
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function editWarehouse(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        Warehouses::where([
            ['company_id', $user->company_id],
            ['id', $request->warehouse_id],
        ])->update([
            'warehouse_name' => $request->warehouse_name,
            'branch_id' => $request->branch_id,
        ]);
    }
    public function deleteWarehouse(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        Warehouses::where([
            ['company_id', $user->company_id],
            ['id', $request->warehouse_id],
        ])->delete();
        Products::where([
            ['company_id', $user->company_id],
            ['warehouse_id', $request->warehouse_id],
        ])->delete();
        TransferWarehouses::where([
            ['company_id', $user->company_id],
            ['from_warehouse', $request->warehouse_id],
        ])->delete();
        TransferWarehouses::where([
            ['company_id', $user->company_id],
            ['to_warehouse', $request->warehouse_id],
        ])->delete();
    }
    public function transferWarehouses(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $product = Products::where([
            ['id', $request->product_id],
            ['company_id', $user->company_id],
            ['warehouse_id', $request->from_warehouse],
        ])->first();
        if ($product !== null) {
            if ($product->warehouse_balance >= $request->quantity) {
                TransferWarehouses::create([
                    'company_id' => $user->company_id,
                    'from_warehouse' => $request->from_warehouse,
                    'to_warehouse' => $request->to_warehouse,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'date' => $request->date,
                    'notes' => $request->notes,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $checkProduct = Products::where([
                    ['company_id', $user->company_id],
                    ['warehouse_id', $request->to_warehouse],
                ])->first();
                if ($checkProduct == null) {
                    Products::create([
                        'company_id' => $user->company_id,
                        'warehouse_id' => $request->to_warehouse,
                        'barcode' => $product->barcode,
                        'warehouse_balance' => $request->quantity,
                        'total_price' => $product->total_price,
                        'product_name' => $product->product_name,
                        'product_unit' => $product->product_unit,
                        'wholesale_price' => $product->wholesale_price,
                        'piece_price' => $product->piece_price,
                        'min_stock' => $product->min_stock,
                        'product_model' => $product->product_model,
                        'category' => $product->category,
                        'sub_category' => $product->sub_category,
                        'description' => $product->description,
                        'image' => $product->image,
                    ]);
                } else {
                    Products::where('id', $checkProduct->id)->update([
                        'warehouse_balance' => $checkProduct->warehouse_balance + $request->quantity
                    ]);
                }
                $getOld = Products::where([
                    ['company_id', $user->company_id],
                    ['warehouse_id', $request->from_warehouse],
                ])->first();
                if ($getOld->warehouse_balance >= $request->quantity) {
                    $getOld->warehouse_balance -= $request->quantity;
                    $getOld->save();
                }
            } else {
                return response()->json(['alert_en' => 'Warehouse balance is not enough', 'alert_ar' => 'رصيد المخزن غير كافي'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'Product is not found', 'alert_ar' => 'المنتج غير موجود في المخزن'], 404);
        }
    }
    public function allTransferWarehouses(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $return = TransferWarehouses::where([
            ['company_id', $user->company_id],
        ])->orderBy('id', 'DESC')->get();
        $return = $return->map(function ($item) {
            return [
                'id' => $item->id,
                'from_warehouse' => $item->f_warehouse ? $item->f_warehouse->warehouse_name : null,
                'to_warehouse' => $item->t_warehouse ? $item->t_warehouse->warehouse_name : null,
                'product_name' => $item->product ? $item->product->product_name : null,
                'quantity' => $item->quantity,
                'date' => $item->date,
                'notes' => $item->notes,
            ];
        });
        return $return;
    }
    public function warehouseInventory(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($request->from_date !== null) {
            if ($request->warehouse_id !== null) {
                return Products::where([
                    ['company_id', $user->company_id],
                    ['warehouse_id', $request->warehouse_id],
                ])->whereBetween('created_at', [$request->from_date, $request->to_date])->orderBy('id', 'DESC')->get();
            } else {
                return Products::where([
                    ['company_id', $user->company_id],
                ])->whereBetween('created_at', [$request->from_date, $request->to_date])->orderBy('id', 'DESC')->get();
            }
        } else {
            if ($request->warehouse_id !== null) {
                return Products::where([
                    ['company_id', $user->company_id],
                    ['warehouse_id', $request->warehouse_id],
                ])->orderBy('id', 'DESC')->get();
            } else {
                return Products::where([
                    ['company_id', $user->company_id],
                ])->orderBy('id', 'DESC')->get();
            }
        }
    }
}
