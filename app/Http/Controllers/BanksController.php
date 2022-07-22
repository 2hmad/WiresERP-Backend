<?php

namespace App\Http\Controllers;

use App\Models\BankActivities;
use App\Models\Banks;
use App\Models\BanksTransfer;
use App\Models\BankToSafe;
use App\Models\Safes;
use App\Models\SafeToBank;
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
    public function deleteBank(Request $request, $id)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $bank = Banks::where([
                ['company_id', $user->company_id],
                ['id', '=', $id]
            ])->first();
            if ($bank !== null) {
                Banks::where([
                    ['id', '=', $id],
                    ['company_id', '=', $user->company_id],
                ])->delete();
                BankActivities::where([
                    ['bank_id', '=', $id],
                    ['company_id', '=', $user->company_id],
                ])->delete();
                BankToSafe::where([
                    ['from_bank', '=', $id],
                    ['company_id', '=', $user->company_id],
                ])->delete();
                SafeToBank::where([
                    ['to_bank', '=', $id],
                    ['company_id', '=', $user->company_id],
                ])->delete();
                BanksTransfer::where([
                    ['from_bank', '=', $id],
                    ['company_id', '=', $user->company_id],
                ])->delete();
                BanksTransfer::where([
                    ['to_bank', '=', $id],
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
                    'bank_name' => $item->bank ? $item->bank->bank_name : null,
                    'amount' => $item->amount,
                    'notes' => $item->notes,
                    'admin' => $item->user ? $item->user->full_name : null
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
    public function deleteBankActivity(Request $request, $id)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $checkActivity = BankActivities::where([
                ['id', $id],
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
    public function transferBanks(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $transfers = BanksTransfer::where('company_id', $user->company_id)->with(['user', 'f_bank', "t_bank"])->orderBy('id', 'DESC')->get();
            $transfers = $transfers->map(function ($item) {
                return [
                    'id' => $item->id,
                    'from_bank' => $item->f_bank->bank_name,
                    'to_bank' => $item->t_bank->bank_name,
                    'amount' => $item->amount,
                    'notes' => $item->notes,
                    'admin' => $item->user->full_name
                ];
            });
            return $transfers;
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function addTransferBanks(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $checkFrom = Banks::where([
                ['id', $request->from_bank],
                ['company_id', $user->company_id]
            ])->first();
            $checkTo = Banks::where([
                ['id', $request->to_bank],
                ['company_id', $user->company_id]
            ])->first();
            if ($checkFrom !== null && $checkTo !== null) {
                if ($checkFrom->bank_balance >= $request->amount) {
                    $checkFrom->bank_balance -= $request->amount;
                    $checkFrom->save();
                    $checkTo->bank_balance += $request->amount;
                    $checkTo->save();
                    BanksTransfer::create([
                        'company_id' => $user->company_id,
                        'user_id' => $user->id,
                        'from_bank' => $request->from_bank,
                        'to_bank' => $request->to_bank,
                        'amount' => $request->amount,
                        'notes' => $request->notes,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    return response()->json(['alert_en' => 'Account balance is not enough', 'alert_ar' => 'رصيد الحساب ليس كافي'], 400);
                }
            } else {
                return response()->json(['alert_en' => 'Bank is not exist', 'alert_ar' => 'الحساب البنكي غير موجودة'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteTransferBanks(Request $request, $id)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $checkTransfer = BanksTransfer::where([
                ['id', $id],
                ['company_id', $user->company_id]
            ])->first();
            if ($checkTransfer !== null) {
                $checkFrom = Banks::where([
                    ['id', $checkTransfer->from_bank],
                    ['company_id', $user->company_id]
                ])->first();
                $checkTo = Banks::where([
                    ['id', $checkTransfer->to_bank],
                    ['company_id', $user->company_id]
                ])->first();
                if ($checkFrom !== null && $checkTo !== null) {
                    $checkFrom->bank_balance += $checkTransfer->amount;
                    $checkFrom->save();
                    $checkTo->bank_balance -= $checkTransfer->amount;
                    $checkTo->save();
                    $checkTransfer->delete();
                } else {
                    return response()->json(['alert_en' => 'Bank is not exist', 'alert_ar' => 'الحساب البنكي غير موجودة'], 400);
                }
            } else {
                return response()->json(['alert_en' => 'Process not exists', 'alert_ar' => 'العملية غير موجود'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function bankToSafe(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            // get bank to safe transfers processess
            $bankToSafe = BankToSafe::where('company_id', $user->company_id)->with(['user', 'bank', 'safe'])->orderBy('id', 'DESC')->get();
            $bankToSafe = $bankToSafe->map(function ($item) {
                return [
                    'id' => $item->id,
                    'bank' => $item->bank->bank_name,
                    'safe' => $item->safe->safe_name,
                    'amount' => $item->amount,
                    'notes' => $item->notes,
                    'admin' => $item->user->full_name
                ];
            });
            return $bankToSafe;
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function addBankToSafe(Request $request)
    {
        // transfer funds from bank to safe
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $checkBank = Banks::where([
                ['id', $request->bank_id],
                ['company_id', $user->company_id]
            ])->first();
            $checkSafe = Safes::where([
                ['id', $request->safe_id],
                ['company_id', $user->company_id]
            ])->first();
            if ($checkBank !== null && $checkSafe !== null) {
                if ($checkBank->bank_balance >= $request->amount) {
                    $checkBank->bank_balance -= $request->amount;
                    $checkBank->save();
                    $checkSafe->safe_balance += $request->amount;
                    $checkSafe->save();
                    BankToSafe::create([
                        'company_id' => $user->company_id,
                        'user_id' => $user->id,
                        'from_bank' => $request->bank_id,
                        'to_safe' => $request->safe_id,
                        'amount' => $request->amount,
                        'notes' => $request->notes,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    return response()->json(['alert_en' => 'Account balance is not enough', 'alert_ar' => 'رصيد الحساب ليس كافي'], 400);
                }
            } else {
                return response()->json(['alert_en' => 'Bank or Safe is not exist', 'alert_ar' => 'الحساب البنكي او الخزينة غير موجودة'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteBankToSafe(Request $request, $id)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            // Delete bank to safe transfer process
            $checkTransfer = BankToSafe::where([
                ['id', $id],
                ['company_id', $user->company_id]
            ])->first();
            if ($checkTransfer !== null) {
                $checkFrom = Banks::where([
                    ['id', $checkTransfer->from_bank],
                    ['company_id', $user->company_id]
                ])->first();
                $checkTo = Safes::where([
                    ['id', $checkTransfer->to_safe],
                    ['company_id', $user->company_id]
                ])->first();
                if ($checkFrom !== null && $checkTo !== null) {
                    $checkFrom->bank_balance += $checkTransfer->amount;
                    $checkFrom->save();
                    $checkTo->safe_balance -= $checkTransfer->amount;
                    $checkTo->save();
                    $checkTransfer->delete();
                } else {
                    return response()->json(['alert_en' => 'Bank is not exist', 'alert_ar' => 'الحساب البنكي غير موجودة'], 400);
                }
            } else {
                return response()->json(['alert_en' => 'Process not exists', 'alert_ar' => 'العملية غير موجود'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function safeToBank(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            // get bank to safe transfers processess
            $safeToBank = SafeToBank::where('company_id', $user->company_id)->with(['user', 'bank', 'safe'])->orderBy('id', 'DESC')->get();
            $safeToBank = $safeToBank->map(function ($item) {
                return [
                    'id' => $item->id,
                    'safe' => $item->safe->safe_name,
                    'bank' => $item->bank->bank_name,
                    'amount' => $item->amount,
                    'notes' => $item->notes,
                    'admin' => $item->user->full_name
                ];
            });
            return $safeToBank;
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function addSafeToBank(Request $request)
    {
        // transfer funds from safe to bank
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $checkSafe = Safes::where([
                ['id', $request->safe_id],
                ['company_id', $user->company_id]
            ])->first();
            $checkBank = Banks::where([
                ['id', $request->bank_id],
                ['company_id', $user->company_id]
            ])->first();
            if ($checkSafe !== null && $checkBank !== null) {
                if ($checkSafe->safe_balance >= $request->amount) {
                    $checkSafe->safe_balance -= $request->amount;
                    $checkSafe->save();
                    $checkBank->bank_balance += $request->amount;
                    $checkBank->save();
                    SafeToBank::create([
                        'company_id' => $user->company_id,
                        'user_id' => $user->id,
                        'from_safe' => $request->safe_id,
                        'to_bank' => $request->bank_id,
                        'amount' => $request->amount,
                        'notes' => $request->notes,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    return response()->json(['alert_en' => 'Account balance is not enough', 'alert_ar' => 'رصيد الحساب ليس كافي'], 400);
                }
            } else {
                return response()->json(['alert_en' => 'Bank or Safe is not exist', 'alert_ar' => 'الحساب البنكي او الخزينة غير موجودة'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteSafeToBank(Request $request, $id)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            // Delete bank to safe transfer process
            $checkTransfer = SafeToBank::where([
                ['id', $id],
                ['company_id', $user->company_id]
            ])->first();
            if ($checkTransfer !== null) {
                $checkFrom = Safes::where([
                    ['id', $checkTransfer->from_safe],
                    ['company_id', $user->company_id]
                ])->first();
                $checkTo = Banks::where([
                    ['id', $checkTransfer->to_bank],
                    ['company_id', $user->company_id]
                ])->first();
                if ($checkFrom !== null && $checkTo !== null) {
                    $checkFrom->safe_balance += $checkTransfer->amount;
                    $checkFrom->save();
                    $checkTo->bank_balance -= $checkTransfer->amount;
                    $checkTo->save();
                    $checkTransfer->delete();
                } else {
                    return response()->json(['alert_en' => 'Bank or Safe is not exist', 'alert_ar' => 'الحساب البنكي او الخزينة غير موجودة'], 400);
                }
            } else {
                return response()->json(['alert_en' => 'Process not exists', 'alert_ar' => 'العملية غير موجود'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
}
