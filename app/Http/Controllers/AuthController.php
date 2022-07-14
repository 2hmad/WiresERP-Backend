<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use App\Models\Companies;
use App\Models\Fiscals;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $checkCompany = Companies::where('name', $request->company_name)->first();
        if ($checkCompany == null) {
            $checkUser = Users::where('email', $request->manager_email)->first();
            if ($checkUser == null) {
                $addCompany = Companies::create([
                    'name' => $request->company_name,
                    'phone' => $request->company_phone,
                    'country' => $request->company_country,
                    'currency' => $request->company_currency,
                    'logo' => "company_placeholder.png",
                    'status' => "active",
                ]);
                $createBranch = Branches::create([
                    'company_id' => $addCompany->id,
                    'branch_name' => "Main branch",
                    'branch_phone' => $request->company_phone
                ]);
                Fiscals::create([
                    'company_id' => $addCompany->id,
                    'fiscal_year' => $request->fiscal_year,
                    'start_date' => $request->fiscal_start_date,
                    'end_date' => $request->fiscal_end_date,
                ]);
                $addUser = Users::create([
                    'full_name' => $request->manager_name,
                    'email' => $request->manager_email,
                    'phone' => $request->manager_phone,
                    'password' => Hash::make($request->manager_password),
                    'role' => 'manager',
                    'token' => 'Bearer ' . md5(time()),
                    'company_id' => $addCompany->id,
                    'branch_id' => $createBranch->id,
                    'status' => 'active',
                    'image' => 'user_placeholder.png'
                ]);
                return $addUser;
            } else {
                return response()->json(['alert_en' => 'User already exists', 'alert_ar' => 'المستخدم موجود بالفعل'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'Company already exists', 'alert_ar' => 'الشركة موجودة بالفعل'], 404);
        }
    }
    public function login(Request $request)
    {
        $user = Users::where('email', $request->email)->first();
        if ($user == null) {
            return response()->json(['alert_en' => 'User not found', 'alert_ar' => 'المستخدم غير موجود'], 404);
        } else {
            if (Hash::check($request->password, $user->password)) {
                return $user;
            } else {
                return response()->json(['alert_en' => 'Password is incorrect', 'alert_ar' => 'كلمة المرور غير صحيحة'], 404);
            }
        }
    }
}
