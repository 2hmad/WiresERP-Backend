<?php

namespace App\Http\Controllers;

use App\Models\BasicSettings;
use App\Models\Companies;
use App\Models\Fiscals;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    public function settings(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $company = Companies::where('id', $user->company_id)->with('fiscal')->get();
        if ($company !== null) {
            $company = $company->map(function ($item) {
                return [
                    "id" => $item->id,
                    "name" => $item->name,
                    "phone" => $item->phone,
                    "country" => $item->country,
                    "business_field" => $item->business_field,
                    "currency" => $item->currency,
                    "tax_number" => $item->tax_number,
                    "civil_registration_number" => $item->civil_registration_number,
                    "tax_value_added" => $item->tax_value_added,
                    "logo" => $item->logo,
                    "company_stamp" => $item->company_stamp,
                    "status" => $item->status,
                    "fiscal_year" => $item->fiscal->fiscal_year,
                    "fiscal_start_date" => $item->fiscal->start_date,
                    "fiscal_end_date" => $item->fiscal->end_date,
                ];
            });
            return $company;
        }
    }
    public function MainSettings(Request $request)
    {
        $validated = $request->validate([
            'logo' => 'nullable|mimes:jpeg,png,jpg,svg|max:2048',
            'stamp' => 'nullable|mimes:jpeg,png,jpg,svg|max:2048',
        ]);
        if ($validated) {
            $user = Users::where('token', $request->header('Authorization'))->first();
            $company = Companies::where('id', $user->company_id)->first();
            if ($user->role == 'manager') {
                if ($company !== null) {
                    if ($request->hasFile('logo')) {
                        $image = $request->file('logo');
                        $name = 'logo' . '.' . $image->getClientOriginalExtension();
                        $destinationPath = public_path('/storage/companies/company-' . $user->company_id);
                        $image->move($destinationPath, $name);
                        $company->logo = $name;
                    }
                    if ($request->hasFile('stamp')) {
                        $image = $request->file('stamp');
                        $name = 'stamp' . '.' . $image->getClientOriginalExtension();
                        $destinationPath = public_path('/storage/companies/company-' . $user->company_id);
                        $image->move($destinationPath, $name);
                        $company->company_stamp = $name;
                    }
                    $company->name = $request->name;
                    $company->founder_name = $request->founder_name;
                    $company->business_field = $request->business_field;
                    $company->address = $request->address;
                    $company->phone = $request->phone;
                    $company->save();
                } else {
                    return response()->json(['alert_en' => 'Company not found', 'alert_ar' => 'الشركة غير موجودة'], 404);
                }
            } else {
                return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'Files not valid', 'alert_ar' => 'الملفات غير صالحة'], 404);
        }
    }
    public function ExtraSettings(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $company = Companies::where('id', $user->company_id)->first();
        if ($user->role == 'manager') {
            if ($company !== null) {
                $company->country = $request->country;
                $company->currency = $request->currency;
                $company->save();
            } else {
                return response()->json(['alert_en' => 'Company not found', 'alert_ar' => 'الشركة غير موجودة'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function TaxsSettings(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $company = Companies::where('id', $user->company_id)->first();
        if ($user->role == 'manager') {
            if ($company !== null) {
                $company->tax_number = $request->tax_number;
                $company->civil_registration_number = $request->civil_registration_number;
                $company->tax_value_added = $request->tax_value_added;
                $company->save();
            } else {
                return response()->json(['alert_en' => 'Company not found', 'alert_ar' => 'الشركة غير موجودة'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
    public function FiscalSettings(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $fiscal = Fiscals::where('company_id', $user->company_id)->first();
        if ($user->role == 'manager') {
            if ($fiscal !== null) {
                $fiscal->fiscal_year = $request->fiscal_year;
                $fiscal->start_date = Carbon::parse($request->start_date)->format('Y-m-d');
                $fiscal->end_date = Carbon::parse($request->end_date)->format('Y-m-d');
                $fiscal->save();
            } else {
                return response()->json(['alert_en' => 'Fiscals not found', 'alert_ar' => 'السنة المالية غير موجودة'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'You are not authorized', 'alert_ar' => 'ليس لديك صلاحية'], 400);
        }
    }
}
