<?php

namespace App\Http\Controllers;

use App\Models\Products;
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
            DB::table('transfer_warehouses')->insert([
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
        } else {
            return response()->json(['alert_en' => 'Warehouse balance is not enough', 'alert_ar' => 'رصيد المخزن غير كافي'], 404);
        }
    }
}
