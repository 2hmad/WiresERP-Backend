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
        return Warehouses::where('company_id', $user->company_id)->get();
    }
    public function addWarehouse(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        Warehouses::create([
            'company_id' => $user->company_id,
            'warehouse_name' => $request->warehouse_name,
            'branch_id' => $request->branch_id,
        ]);
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
                $checkTransfer = TransferWarehouses::where([
                    ['company_id', $user->company_id],
                    ['product_id', $request->product_id],
                    ['from_warehouse', $request->from_warehouse],
                    ['to_warehouse', $request->to_warehouse],
                ])->first();
                if ($checkTransfer == null) {
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
                } else {
                    $checkTransfer->quantity += $request->quantity;
                    $checkTransfer->save();
                }
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
                if ($getOld->warehouse_balance > $request->quantity) {
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
    public function warehouseInventory(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($request->from_date !== null) {
            if ($request->warehouse_id !== null) {
                $return = TransferWarehouses::where([
                    ['company_id', $user->company_id],
                    ['to_warehouse', $request->warehouse_id],
                ])->whereBetween('created_at', [$request->from_date, $request->to_date])->with(['f_warehouse', 't_warehouse', 'product'])->get();
                $return = $return->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'from_warehouse' => $item->f_warehouse->warehouse_name,
                        'to_warehouse' => $item->t_warehouse->warehouse_name,
                        'product_name' => $item->product->product_name,
                        'quantity' => $item->quantity,
                        'date' => $item->date,
                        'notes' => $item->notes,
                    ];
                });
                return $return;
            } else {
                $return = TransferWarehouses::where([
                    ['company_id', $user->company_id]
                ])->whereBetween('created_at', [$request->from_date, $request->to_date])->with(['f_warehouse', 't_warehouse', 'product'])->get();
                $return = $return->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'from_warehouse' => $item->f_warehouse->warehouse_name,
                        'to_warehouse' => $item->t_warehouse->warehouse_name,
                        'product_name' => $item->product->product_name,
                        'quantity' => $item->quantity,
                        'date' => $item->date,
                        'notes' => $item->notes,
                    ];
                });
                return $return;
            }
        } else {
            if ($request->warehouse_id !== null) {
                $return = TransferWarehouses::where([
                    ['company_id', $user->company_id],
                    ['to_warehouse', $request->warehouse_id],
                ])->with(['f_warehouse', 't_warehouse', 'product'])->get();
                $return = $return->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'from_warehouse' => $item->f_warehouse->warehouse_name,
                        'to_warehouse' => $item->t_warehouse->warehouse_name,
                        'product_name' => $item->product->product_name,
                        'quantity' => $item->quantity,
                        'date' => $item->date,
                        'notes' => $item->notes,
                    ];
                });
                return $return;
            } else {
                $return = TransferWarehouses::where([
                    ['company_id', $user->company_id]
                ])->with(['f_warehouse', 't_warehouse', 'product'])->get();
                $return = $return->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'from_warehouse' => $item->f_warehouse->warehouse_name,
                        'to_warehouse' => $item->t_warehouse->warehouse_name,
                        'product_name' => $item->product->product_name,
                        'quantity' => $item->quantity,
                        'date' => $item->date,
                        'notes' => $item->notes,
                    ];
                });
                return $return;
            }
        }
    }
}
