<?php

namespace App\Http\Controllers;

use App\Models\Safes;
use App\Models\Users;
use Illuminate\Http\Request;

class BanksController extends Controller
{
    public function safes(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        return Safes::where('company_id', $user->company_id)->get();
    }
    public function addSafe(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $safe = Safes::where([
                ['company_id', $user->company_id],
                ['safe_name', '=', $request->safe_name],
                ['branch_id', '=', $request->branch_id]
            ])->first();
            if ($safe == null) {
                Safes::create([
                    'company_id' => $user->company_id,
                    'safe_name' => $request->safe_name,
                    'branch_id' => $request->branch_id,
                    'safe_balance' => $request->safe_balance,
                    'safe_type' => $request->safe_type,
                ]);
            } else {
                return response()->json(['alert_en' => 'Safe already exists', 'alert_ar' => 'تم اضافة الخزنة من قبل'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteSafe(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            return Safes::where([
                ['id', '=', $request->safe_id],
                ['company_id', '=', $user->company_id],
            ])->delete();
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
}
