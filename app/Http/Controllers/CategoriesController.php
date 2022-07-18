<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\SubCategory;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function categories(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        return DB::table('categories')->where('company_id', $user->company_id)->get();
    }
    public function addCategory(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $category = DB::table('categories')->where([
            ['company_id', '=', $user->company_id],
            ['category_name', '=', $request->category_name]
        ])->first();
        if ($category == null) {
            DB::table('categories')->insert([
                'company_id' => $user->company_id,
                'category_name' => $request->category_name,
                'type' => $request->type
            ]);
        } else {
            return response()->json(['alert_en' => 'Category already exists', 'alert_ar' => 'تم اضافة الفئة من قبل'], 404);
        }
    }
    public function deleteCategory(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $category = DB::table('categories')->where([
            ['company_id', '=', $user->company_id],
            ['id', '=', $request->cat_id]
        ])->first();
        if ($category !== null) {
            DB::table('categories')->where([
                ['company_id', '=', $user->company_id],
                ['id', '=', $request->cat_id]
            ])->delete();
            DB::table('sub_categories')->where([
                ['company_id', '=', $user->company_id],
                ['category_id', '=', $request->cat_id]
            ])->delete();
            Products::where([
                ['company_id', '=', $user->company_id],
                ['category', $request->cat_id]
            ])->delete();
        } else {
            return response()->json(['alert_en' => 'Category not found', 'alert_ar' => 'الفئة غير موجودة'], 404);
        }
    }
    public function editCategory(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $category = DB::table('categories')->where([
            ['company_id', '=', $user->company_id],
            ['id', '=', $request->cat_id]
        ])->first();
        if ($category !== null) {
            DB::table('categories')->where([
                ['company_id', '=', $user->company_id], ['id', '=', $request->cat_id]
            ])->update([
                'category_name' => $request->category_name,
                'type' => $request->type
            ]);
        } else {
            return response()->json(['alert_en' => 'Category not found', 'alert_ar' => 'الفئة غير موجودة'], 404);
        }
    }
    public function sub_categories(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        return SubCategory::where('company_id', $user->company_id)->with('category')->get();
    }
    public function addSubCategory(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $sub_category = SubCategory::where([
            ['company_id', '=', $user->company_id],
            ['sub_category_name', '=', $request->sub_category_name],
            ['category_id', '=', $request->category_id]
        ])->first();
        if ($sub_category == null) {
            SubCategory::create([
                'company_id' => $user->company_id,
                'category_id' => $request->category_id,
                'sub_category_name' => $request->sub_category_name,
            ]);
        } else {
            return response()->json(['alert_en' => 'Category already exists', 'alert_ar' => 'تم اضافة الفئة من قبل'], 404);
        }
    }
    public function deleteSubCategory(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $category = SubCategory::where([
            ['company_id', '=', $user->company_id],
            ['id', '=', $request->sub_cat_id]
        ])->first();
        if ($category !== null) {
            SubCategory::where([
                ['company_id', '=', $user->company_id], ['id', '=', $request->sub_cat_id]
            ])->delete();
            Products::where([
                ['company_id', '=', $user->company_id],
                ['sub_category', $request->sub_cat_id]
            ])->delete();
        } else {
            return response()->json(['alert_en' => 'Category not found', 'alert_ar' => 'الفئة غير موجودة'], 404);
        }
    }
    public function editSubCategory(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $category = SubCategory::where([
            ['company_id', '=', $user->company_id],
            ['id', '=', $request->sub_cat_id]
        ])->first();
        if ($category !== null) {
            SubCategory::where([
                ['company_id', '=', $user->company_id], ['id', '=', $request->sub_cat_id]
            ])->update([
                'category_id' => $request->category_id,
                'sub_category_name' => $request->sub_category_name,
            ]);
        } else {
            return response()->json(['alert_en' => 'Category not found', 'alert_ar' => 'الفئة غير موجودة'], 404);
        }
    }
}
