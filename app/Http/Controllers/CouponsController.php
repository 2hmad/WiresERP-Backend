<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Clients;
use App\Models\Coupons;
use App\Models\Products;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    public function coupons(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        return Coupons::where('company_id', $user->company_id)->orderBy('id', 'DESC')->get();
    }
    public function addCoupon(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $coupon = Coupons::where([
            ['company_id', $user->company_id],
            ['code', $request->code]
        ])->first();
        if ($coupon == null) {
            if ($request->section == 'clients') {
                $client = Clients::where([
                    ['company_id', $user->company_id],
                    ['id', $request->client_id]
                ])->first();
                if ($client == null) {
                    return response()->json(['alert_en' => 'Client not found', 'alert_ar' => 'عميل غير موجود'], 404);
                } else {
                    Coupons::create([
                        'company_id' => $user->company_id,
                        'code' => $request->code,
                        'amount' => $request->discount,
                        'expire_date' => Carbon::parse($request->expire_date)->format('Y-m-d'),
                        'section' => $request->section,
                        'client_id' => $request->client_id,
                        'category_id' => $request->category_id,
                        "product_id" => $request->product_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else if ($request->section == 'items') {
                $category = Category::where([
                    ['company_id', $user->company_id],
                    ['id', $request->category_id]
                ])->first();
                if ($category == null) {
                    return response()->json(['alert_en' => 'Category not found', 'alert_ar' => 'فئة غير موجودة'], 404);
                } else {
                    Coupons::create([
                        'company_id' => $user->company_id,
                        'code' => $request->code,
                        'amount' => $request->discount,
                        'expire_date' => Carbon::parse($request->expire_date)->format('Y-m-d'),
                        'section' => $request->section,
                        'client_id' => $request->client_id,
                        'category_id' => $request->category_id,
                        "product_id" => $request->product_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else if ($request->section == 'products') {
                $product = Products::where([
                    ['company_id', $user->company_id],
                    ['id', $request->product_id]
                ])->first();
                if ($product == null) {
                    return response()->json(['alert_en' => 'Product not found', 'alert_ar' => 'منتج غير موجود'], 404);
                } else {
                    Coupons::create([
                        'company_id' => $user->company_id,
                        'code' => $request->code,
                        'amount' => $request->discount,
                        'expire_date' => Carbon::parse($request->expire_date)->format('Y-m-d'),
                        'section' => $request->section,
                        'client_id' => $request->section == 'clients' ? $request->item_id : null,
                        'category_id' => $request->section == 'categories' ? $request->item_id : null,
                        "product_id" => $request->section == 'products' ? $request->item_id : null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else {
                return response()->json(['alert_en' => 'Section not found', 'alert_ar' => 'القسم غير موضح'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'Coupon already exists', 'alert_ar' => 'تم اضافة الكوبون من قبل'], 404);
        }
    }
    public function editCoupon(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $coupon = Coupons::where([
            ['company_id', $user->company_id],
            ['id', $request->id]
        ])->first();
        if ($coupon !== null) {
            if ($request->section == 'clients') {
                $client = Clients::where([
                    ['company_id', $user->company_id],
                    ['id', $request->client_id]
                ])->first();
                if ($client == null) {
                    return response()->json(['alert_en' => 'Client not found', 'alert_ar' => 'عميل غير موجود'], 404);
                } else {
                    Coupons::where([
                        ['company_id', $user->company_id],
                        ['id', $request->id]
                    ])->update([
                        'company_id' => $user->company_id,
                        'code' => $request->code,
                        'amount' => $request->discount,
                        'expire_date' => Carbon::parse($request->expire_date)->format('Y-m-d'),
                        'section' => $request->section,
                        'client_id' => $request->section == 'clients' ? $request->item_id : null,
                        'category_id' => $request->section == 'categories' ? $request->item_id : null,
                        "product_id" => $request->section == 'products' ? $request->item_id : null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else if ($request->section == 'items') {
                $category = Category::where([
                    ['company_id', $user->company_id],
                    ['id', $request->category_id]
                ])->first();
                if ($category == null) {
                    return response()->json(['alert_en' => 'Category not found', 'alert_ar' => 'فئة غير موجودة'], 404);
                } else {
                    Coupons::where([
                        ['company_id', $user->company_id],
                        ['id', $request->id]
                    ])->update([
                        'company_id' => $user->company_id,
                        'code' => $request->code,
                        'amount' => $request->discount,
                        'expire_date' => Carbon::parse($request->expire_date)->format('Y-m-d'),
                        'section' => $request->section,
                        'client_id' => $request->section == 'clients' ? $request->item_id : null,
                        'category_id' => $request->section == 'categories' ? $request->item_id : null,
                        "product_id" => $request->section == 'products' ? $request->item_id : null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else if ($request->section == 'products') {
                $product = Products::where([
                    ['company_id', $user->company_id],
                    ['id', $request->product_id]
                ])->first();
                if ($product == null) {
                    return response()->json(['alert_en' => 'Product not found', 'alert_ar' => 'منتج غير موجود'], 404);
                } else {
                    Coupons::where([
                        ['company_id', $user->company_id],
                        ['id', $request->id]
                    ])->update([
                        'company_id' => $user->company_id,
                        'code' => $request->code,
                        'amount' => $request->discount,
                        'expire_date' => Carbon::parse($request->expire_date)->format('Y-m-d'),
                        'section' => $request->section,
                        'client_id' => $request->section == 'clients' ? $request->item_id : null,
                        'category_id' => $request->section == 'categories' ? $request->item_id : null,
                        "product_id" => $request->section == 'products' ? $request->item_id : null,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else {
                return response()->json(['alert_en' => 'Section not found', 'alert_ar' => 'القسم غير موضح'], 404);
            }
        } else {
            return response()->json(['alert_en' => 'Coupon not found', 'alert_ar' => 'الكوبون غير موجود'], 404);
        }
    }
    public function deleteCoupon(Request $request, $id)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $coupon = Coupons::where([
            ['company_id', $user->company_id],
            ['id', $id]
        ])->first();
        if ($coupon !== null) {
            Coupons::where([
                ['company_id', $user->company_id],
                ['id', $id]
            ])->delete();
        } else {
            return response()->json(['alert_en' => 'Coupon not found', 'alert_ar' => 'الكوبون غير موجود'], 404);
        }
    }
}
