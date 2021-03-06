<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use App\Models\Users;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    public function branches(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        return Branches::where('company_id', $user->company_id)->orderBy('id', 'DESC')->get();
    }
    public function addBranch(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $check = Branches::where([
                ['company_id', $user->company_id],
                ['branch_name', '=', $request->branch_name],
            ])->first();
            if ($check !== null) {
                return response()->json(['alert_en' => 'Branch already exists', 'alert_ar' => 'تم اضافة الفرع من قبل'], 400);
            } else {
                Branches::create([
                    'company_id' => $user->company_id,
                    'branch_name' => $request->branch_name,
                    'branch_phone' => $request->branch_phone,
                    'branch_address' => $request->branch_address,
                    'commercial_registration_number' => $request->commercial_registration_number,
                ]);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteBranch(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            return Branches::where([
                ['id', '=', $request->branch_id],
                ['company_id', '=', $user->company_id],
            ])->delete();
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function editBranch(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            return Branches::where([
                ['id', '=', $request->branch_id],
                ['company_id', '=', $user->company_id],
            ])->update([
                'branch_name' => $request->branch_name,
                'branch_phone' => $request->branch_phone,
                'branch_address' => $request->branch_address,
                'commercial_registration_number' => $request->commercial_registration_number,
            ]);
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
}
