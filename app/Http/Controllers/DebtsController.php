<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Suppliers;
use App\Models\Users;
use Illuminate\Http\Request;

class DebtsController extends Controller
{
    public function clients(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $clients = Clients::where('company_id', $user->company_id)->with('user')->orderBy('id', 'DESC')->get();
        $clients = $clients->map(function ($item) {
            return [
                "id" => $item->id,
                "company_id" => $item->company_id,
                "c_name" => $item->c_name,
                "releated_user" => $item->user ? $item->user->full_name : null,
                "indebt_type" => $item->idebt_type,
                "indebt_amount" => $item->indebt_amount,
                "c_phone" => $item->c_phone,
                "c_address" => $item->c_address,
                "c_notes" => $item->c_notes,
                "deal_type" => $item->deal_type,
                "c_email" => $item->c_email,
                "c_company" => $item->c_company,
                "c_nationality" => $item->c_nationality,
                "c_tax_number" => $item->c_tax_number
            ];
        });
        return $clients;
    }
    public function addClient(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $client = Clients::where([
                ['company_id', '=', $user->company_id],
                ['c_name', '=', $request->c_name]
            ])->first();
            if ($client == null) {
                Clients::create([
                    'company_id' => $user->company_id,
                    'c_name' => $request->c_name,
                    'releated_user' => $request->releated_user ? $request->releated_user : null,
                    'indebt_type' => $request->indebt_type,
                    'indebt_amount' => $request->indebt_amount,
                    'c_phone' => $request->c_phone,
                    'c_address' => $request->c_address,
                    'c_notes' => $request->c_notes,
                    'deal_type' => $request->deal_type,
                    'c_email' => $request->c_email,
                    'c_company' => $request->c_company,
                    'c_nationality' => $request->c_nationality,
                    'c_tax_number' => $request->c_tax_number
                ]);
            } else {
                return response()->json(['alert_en' => 'Client already exist', 'alert_ar' => 'العميل موجود بالفعل'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function editClient(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $client = Clients::where([
                ['company_id', '=', $user->company_id],
                ['id', '=', $request->id]
            ])->first();
            if ($client !== null) {
                Clients::where('id', $request->id)->update([
                    'c_name' => $request->c_name,
                    'releated_user' => $request->releated_user ? $request->releated_user : null,
                    'indebt_type' => $request->indebt_type,
                    'indebt_amount' => $request->indebt_amount,
                    'c_phone' => $request->c_phone,
                    'c_address' => $request->c_address,
                    'c_notes' => $request->c_notes,
                    'deal_type' => $request->deal_type,
                    'c_email' => $request->c_email,
                    'c_company' => $request->c_company,
                    'c_nationality' => $request->c_nationality,
                    'c_tax_number' => $request->c_tax_number
                ]);
            } else {
                return response()->json(['alert_en' => 'Client not found', 'alert_ar' => 'العميل ليس موجود'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteClient(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $check = Clients::where([
                ['company_id', $user->company_id],
                ['id', $request->id]
            ])->first();
            if ($check !== null) {
                Clients::where('id', $request->id)->delete();
            } else {
                return response()->json(['alert_en' => 'Client not found', 'alert_ar' => 'العميل ليس موجود'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function suppliers(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        return Suppliers::where('company_id', $user->company_id)->orderBy('id', 'DESC')->get();
    }
    public function addSupplier(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $client = Suppliers::where([
                ['company_id', '=', $user->company_id],
                ['s_name', '=', $request->s_name]
            ])->first();
            if ($client == null) {
                Suppliers::create([
                    'company_id' => $user->company_id,
                    's_name' => $request->s_name,
                    'indebt_type' => $request->indebt_type,
                    'indebt_amount' => $request->indebt_amount,
                    's_phone' => $request->s_phone,
                    's_address' => $request->s_address,
                    's_notes' => $request->s_notes,
                    'deal_type' => $request->deal_type,
                    's_email' => $request->s_email,
                    's_company' => $request->s_company,
                    's_nationality' => $request->s_nationality,
                    's_tax_number' => $request->s_tax_number
                ]);
            } else {
                return response()->json(['alert_en' => 'Supplier already exist', 'alert_ar' => 'المورد موجود بالفعل'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function editSupplier(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $supplier = Suppliers::where([
                ['company_id', '=', $user->company_id],
                ['id', '=', $request->id]
            ])->first();
            if ($supplier !== null) {
                Suppliers::where('id', $request->id)->update([
                    's_name' => $request->s_name,
                    'indebt_type' => $request->indebt_type,
                    'indebt_amount' => $request->indebt_amount,
                    's_phone' => $request->s_phone,
                    's_address' => $request->s_address,
                    's_notes' => $request->s_notes,
                    'deal_type' => $request->deal_type,
                    's_email' => $request->s_email,
                    's_company' => $request->s_company,
                    's_nationality' => $request->s_nationality,
                    's_tax_number' => $request->s_tax_number
                ]);
            } else {
                return response()->json(['alert_en' => 'Supplier not found', 'alert_ar' => 'المورد ليس موجود'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function deleteSupplier(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $check = Suppliers::where([
                ['company_id', $user->company_id],
                ['id', $request->id]
            ])->first();
            if ($check !== null) {
                Suppliers::where('id', $request->id)->delete();
            } else {
                return response()->json(['alert_en' => 'Supplier not found', 'alert_ar' => 'المورد ليس موجود'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
}
