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
        $product = Products::where('id', $request->product_id)->first();
        if ($product->warehouse_balance >= $request->quantity) {
            TransferWarehouses::create([
                'company_id' => $user->company_id,
                'from' => $request->from_warehouse,
                'to' => $request->to_warehouse,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'date' => $request->date,
                'notes' => $request->notes,
            ]);
            Products::where('id', $request->product_id)->update([
                'warehouse_balance' => $product->warehouse_balance - $request->quantity
            ]);
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
            return response()->json(['alert_en' => 'Warehouse balance is not enough', 'alert_ar' => 'رصيد المخزن غير كافي'], 404);
        }
    }
    public function warehouseInventory(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($request->from_date !== null) {
            return TransferWarehouses::where([
                ['company_id', $user->company_id],
                ['to_warehouse', $request->warehouse_id],
            ])->whereBetween('created_at', [$request->from_date, $request->to_date])->with(['from_warehouse', 'to_warehouse', 'product'])->get();
        } else {
            return TransferWarehouses::where([
                ['company_id', $user->company_id],
                ['to_warehouse', $request->warehouse_id],
            ])->with(['from_warehouse', 'to_warehouse', 'product'])->get();
        }
    }
}
