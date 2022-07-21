<?php

namespace App\Http\Controllers;

use App\Models\Safes;
use App\Models\TransferSafes;
use App\Models\Users;
use Illuminate\Http\Request;

class SafesController extends Controller
{
    public function safes(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $safes = Safes::where('company_id', $user->company_id)->with('branch')->orderBy('id', 'DESC')->get();
        $safes = $safes->map(function ($item) {
            return [
                "id" => $item->id,
                "safe_name" => $item->safe_name,
                "branch_id" => $item->branch_id,
                "branch" => $item->branch ? $item->branch->branch_name : null,
                "safe_balance" => $item->safe_balance,
                "safe_type" => $item->safe_type
            ];
        });
        return $safes;
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
    public function editSafe(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $check = Safes::where([
                ['company_id', $user->company_id],
                ['id', '=', $request->id]
            ])->first();
            if ($check !== null) {
                Safes::where([
                    ['id', $request->id],
                    ['company_id', $user->company_id]
                ])->update([
                    'safe_name' => $request->safe_name,
                    'branch_id' => $request->branch_id,
                    'safe_balance' => $request->safe_balance,
                    'safe_type' => $request->safe_type,
                ]);
            } else {
                return response()->json(['alert_en' => 'Safe not exists', 'alert_ar' => 'الخزينة ليست موجودة'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteSafe(Request $request, $id)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $check = Safes::where([
                ['company_id', $user->company_id],
                ['id', '=', $id]
            ])->first();
            if ($check !== null) {
                return Safes::where([
                    ['id', '=', $id],
                    ['company_id', '=', $user->company_id],
                ])->delete();
            } else {
                return response()->json(['alert_en' => 'Safe not exists', 'alert_ar' => 'الخزينة ليست موجودة'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function allTransfers(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $transfers = TransferSafes::where('company_id', $user->company_id)->with(['fromSafe', 'toSafe'])->orderBy('id', 'DESC')->get();
        $transfers = $transfers->map(function ($item) {
            return [
                "id" => $item->id,
                "from_safe_id" => $item->from_safe,
                "to_safe_id" => $item->to_safe,
                "from_safe" => $item->fromSafe ? $item->fromSafe->safe_name : null,
                "to_safe" => $item->toSafe ? $item->toSafe->safe_name : null,
                "amount" => 100,
                "notes" => null
            ];
        });
        return $transfers;
    }
    public function transferSafes(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $checkFrom = Safes::where([
                ['id', $request->from_safe_id],
                ['company_id', $user->company_id]
            ])->first();
            $checkTo = Safes::where([
                ['id', $request->to_safe_id],
                ['company_id', $user->company_id]
            ])->first();
            if ($checkFrom !== null && $checkTo !== null) {
                if ($checkFrom->safe_balance >= $request->amount) {
                    $checkFrom->safe_balance -= $request->amount;
                    $checkFrom->save();
                    $checkTo->safe_balance += $request->amount;
                    $checkTo->save();
                    TransferSafes::create([
                        'company_id' => $user->company_id,
                        'from_safe' => $request->from_safe_id,
                        'to_safe' => $request->to_safe_id,
                        'amount' => $request->amount,
                        'notes' => $request->notes,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    return response()->json(['alert_en' => 'Safe balance is not enough', 'alert_ar' => 'رصيد الخزنة ليس كافي'], 400);
                }
            } else {
                return response()->json(['alert_en' => 'Safe is not exist', 'alert_ar' => 'الخزنة غير موجودة'], 400);
            }
        }
    }
}
