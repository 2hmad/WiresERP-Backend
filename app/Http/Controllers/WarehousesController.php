<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Warehouses;
use Illuminate\Http\Request;

class WarehousesController extends Controller
{
    public function warehouses(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user == null) {
            return response()->json(['alert_en' => 'Unauthorized', 'alert_ar' => 'غير مصرح'], 401);
        } else {
            return Warehouses::where('company_id', $user->company_id)->get();
        }
    }
    public function addWarehouse(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user == null) {
            return response()->json(['alert_en' => 'Unauthorized', 'alert_ar' => 'غير مصرح'], 401);
        } else {
            Warehouses::create([
                'company_id' => $user->company_id,
                'warehouse_name' => $request->warehouse_name,
                'branch_id' => $request->branch_id,
            ]);
        }
    }
    public function editWarehouse(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user == null) {
            return response()->json(['alert_en' => 'Unauthorized', 'alert_ar' => 'غير مصرح'], 401);
        } else {
            Warehouses::where([
                ['company_id', $user->company_id],
                ['id', $request->warehouse_id],
            ])->update([
                'warehouse_name' => $request->warehouse_name,
                'branch_id' => $request->branch_id,
            ]);
        }
    }
    public function deleteWarehouse(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user == null) {
            return response()->json(['alert_en' => 'Unauthorized', 'alert_ar' => 'غير مصرح'], 401);
        } else {
            Warehouses::where([
                ['company_id', $user->company_id],
                ['id', $request->warehouse_id],
            ])->delete();
        }
    }
}
