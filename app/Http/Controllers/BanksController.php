<?php

namespace App\Http\Controllers;

use App\Models\BankActivities;
use App\Models\Banks;
use App\Models\Users;
use Illuminate\Http\Request;

class BanksController extends Controller
{
    public function banks(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            return Banks::where('company_id', $user->company_id)->get();
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function addBank(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $bank = Banks::where([
                ['company_id', $user->company_id],
                ['bank_name', $request->bank_name]
            ])->first();
            if ($bank == null) {
                Banks::create([
                    'company_id' => $user->company_id,
                    'bank_name' => $request->bank_name,
                    'bank_balance' => $request->bank_balance
                ]);
            } else {
                return response()->json(['alert_en' => 'Bank already exist', 'alert_ar' => 'البنك مضاف من قبل'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function editBank(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $check = Banks::where([
                ['company_id', $user->company_id],
                ['id', '=', $request->id]
            ])->first();
            if ($check !== null) {
                Banks::where([
                    ['id', $request->id],
                    ['company_id', $user->company_id]
                ])->update([
                    'bank_name' => $request->bank_name,
                    'bank_balance' => $request->bank_balance,
                ]);
            } else {
                return response()->json(['alert_en' => 'Bank not exists', 'alert_ar' => 'البنك غير موجود'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteBank(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $bank = Banks::where([
                ['company_id', $user->company_id],
                ['id', '=', $request->id]
            ])->first();
            if ($bank !== null) {
                return Banks::where([
                    ['id', '=', $request->id],
                    ['company_id', '=', $user->company_id],
                ])->delete();
            } else {
                return response()->json(['alert_en' => 'Bank not exists', 'alert_ar' => 'البنك غير موجود'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function bankActivities(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $activities = BankActivities::where('company_id', $user->company_id)->with(['user', 'bank'])->orderBy('id', 'DESC')->get();
            $activities = $activities->map(function ($item) {
                return [
                    'id' => $item->id,
                    'process_type' => $item->type,
                    'bank_name' => $item->bank->bank_name,
                    'amount' => $item->amount,
                    'notes' => $item->notes,
                    'admin' => $item->user->name
                ];
            });
            return $activities;
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function addBankActivity(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $checkBank = Banks::where([
                ['id', $request->bank_id],
                ['company_id', $user->company_id]
            ])->first();
            if ($checkBank !== null) {
                if ($request->type == 'deposit') {
                    $checkBank->bank_balance += $request->amount;
                } else if ($request->type == 'withdraw') {
                    if ($checkBank->bank_balance < $request->amount) {
                        return response()->json(['alert_en' => 'Not enough balance', 'alert_ar' => 'رصيد غير كافي'], 400);
                    } else {
                        $checkBank->bank_balance -= $request->amount;
                    }
                }
                $checkBank->save();
                BankActivities::create([
                    'company_id' => $user->company_id,
                    'user_id' => $user->id,
                    'bank_id' => $request->bank_id,
                    'amount' => $request->amount,
                    'type' => $request->type,
                    'notes' => $request->notes,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                return response()->json(['alert_en' => 'Bank not exists', 'alert_ar' => 'البنك غير موجود'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteBankActivity(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $checkActivity = BankActivities::where([
                ['id', $request->id],
                ['company_id', $user->company_id]
            ])->first();
            if ($checkActivity !== null) {
                $checkBank = Banks::where([
                    ['id', $checkActivity->bank_id],
                    ['company_id', $user->company_id]
                ])->first();
                if ($checkBank !== null) {
                    if ($checkActivity->type == 'deposit') {
                        $checkBank->bank_balance -= $checkActivity->amount;
                    } else if ($checkActivity->type == 'withdraw') {
                        $checkBank->bank_balance += $checkActivity->amount;
                    }
                    $checkBank->save();
                    $checkActivity->delete();
                } else {
                    return response()->json(['alert_en' => 'Bank not exists', 'alert_ar' => 'البنك غير موجود'], 400);
                }
            } else {
                return response()->json(['alert_en' => 'Process not exists', 'alert_ar' => 'العملية غير موجود'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
}
