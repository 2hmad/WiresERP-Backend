<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Products;
use App\Models\SaleBillElements;
use App\Models\SaleBillExtra;
use App\Models\SaleBills;
use App\Models\Users;
use App\Models\Warehouses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleBillController extends Controller
{
    public function addSaleBill(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $client = Clients::where([
            ['company_id', $user->company_id],
            ['id', $request->client_id]
        ])->first();
        $warehouse = Warehouses::where([
            ['company_id', $user->company_id],
            ['id', $request->warehouse_id]
        ])->first();
        $product = Products::where([
            ['company_id', $user->company_id],
            ['id', $request->product_id]
        ])->first();
        if ($client !== null && $warehouse !== null && $product !== null) {
            $sale_bill = SaleBills::create([
                'company_id' => $user->company_id,
                'client_id' => $request->client_id,
                'bill_number' => SaleBills::where('company_id', $user->company_id)->first() ? SaleBills::where('company_id', $user->company_id)->latest()->first()->bill_number + 1 : 1,
                'date_time' => Carbon::parse($request->date_time)->format('Y-m-d H:i:s'),
                'warehouse_id' => $request->warehouse_id,
                'value_added_tax' => $request->value_added_tax,
                'final_total' => $request->final_total,
                'paid' => 0,
                'status' => "active",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]);
            if ($sale_bill) {
                SaleBillElements::create([
                    'company_id' => $user->company_id,
                    'sale_bill_id' => $sale_bill->id,
                    'product_id' => $request->product_id,
                    'product_price' => $request->product_price,
                    'quantity' => $request->quantity,
                    'unit' => $request->unit,
                    'quantity_price' => $request->quantity_price,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
            }
        } else {
            return response()->json(['alert_en' => 'Client or Warehouse or Product not found', 'alert_ar' => 'العميل او المخزن او المنتج غير موجود'], 400);
        }
    }
    public function deleteSaleBill(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $sale_bill = SaleBills::where([
            ['company_id', $user->company_id],
            ['id', $request->id]
        ])->first();
        if ($sale_bill) {
            $sale_bill->delete();
            SaleBillElements::where('sale_bill_id', $request->id)->delete();
            SaleBillExtra::where('sale_bill_id', $request->id)->delete();
        } else {
            return response()->json(['alert_en' => 'Sale Bill not found', 'alert_ar' => 'الفاتورة غير موجودة'], 400);
        }
    }
    public function addProductSaleBill(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $sale_bill = SaleBills::where([
            ['company_id', $user->company_id],
            ['id', $request->sale_bill_id]
        ])->first();
        $product = Products::where([
            ['company_id', $user->company_id],
            ['id', $request->product_id]
        ])->first();
        $checkElement = SaleBillElements::where([
            ['company_id', $user->company_id],
            ['sale_bill_id', $request->sale_bill_id],
            ['product_id', $request->product_id]
        ])->first();
        if ($checkElement == null) {
            if ($sale_bill !== null && $product !== null) {
                SaleBillElements::create([
                    'company_id' => $user->company_id,
                    'sale_bill_id' => $request->sale_bill_id,
                    'product_id' => $request->product_id,
                    'product_price' => $request->product_price,
                    'quantity' => $request->quantity,
                    'unit' => $request->unit,
                    'quantity_price' => $request->quantity_price,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
            } else {
                return response()->json(['alert_en' => 'Sale bill or Product not found', 'alert_ar' => 'الفاتورة او المنتج غير موجود'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'Product already added to this sale bill', 'alert_ar' => 'المنتج موجود بالفعل في هذه الفاتورة'], 400);
        }
    }
    public function deleteProductSaleBill(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $sale_bill = SaleBills::where([
            ['company_id', $user->company_id],
            ['id', $request->sale_bill_id]
        ])->first();
        $product = Products::where([
            ['company_id', $user->company_id],
            ['id', $request->product_id]
        ])->first();
        $checkElement = SaleBillElements::where([
            ['company_id', $user->company_id],
            ['sale_bill_id', $request->sale_bill_id],
            ['product_id', $request->product_id]
        ])->first();
        if ($checkElement !== null) {
            if ($sale_bill !== null && $product !== null) {
                $checkElement->delete();
            } else {
                return response()->json(['alert_en' => 'Sale bill or Product not found', 'alert_ar' => 'الفاتورة او المنتج غير موجود'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'Product not found in this sale bill', 'alert_ar' => 'المنتج غير موجود في هذه الفاتورة'], 400);
        }
    }
    public function addSaleBillExtra(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $invoice = SaleBills::where([
            ['company_id', $user->company_id],
            ['id', $request->id]
        ])->first();
        if ($invoice !== null) {
            $extra_bill = SaleBillExtra::where([
                ['company_id', $user->company_id],
                ['sale_bill_id', $invoice->id]
            ])->first();
            if ($extra_bill !== null) {
                $check = SaleBillExtra::where([
                    ['company_id', $user->company_id],
                    ['sale_bill_id', $invoice->id],
                    ['action', $request->action],
                ])->first();
                if ($check !== null) {
                    $check->update([
                        'action_type' => $request->action_type,
                        'value' => $request->value,
                        "updated_at" => Carbon::now(),
                    ]);
                } else {
                    SaleBillExtra::create([
                        'company_id' => $user->company_id,
                        'sale_bill_id' => $invoice->id,
                        'action' => $request->action,
                        'action_type' => $request->action_type,
                        'value' => $request->value,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ]);
                }
            } else {
                SaleBillExtra::create([
                    'company_id' => $user->company_id,
                    'sale_bill_id' => $invoice->id,
                    'action' => $request->action,
                    'action_type' => $request->action_type,
                    'value' => $request->value,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
            }
        } else {
            return response()->json(['alert_en' => 'Invoice not found', 'alert_ar' => 'الفاتورة غير موجودة'], 400);
        }
    }
    public function addRecordPayment(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $invoice = SaleBills::where([
            ['company_id', $user->company_id],
            ['id', $request->id]
        ])->first();
        if ($invoice !== null) {
            if ($invoice->paid < $invoice->final_total && $request->value <= $invoice->final_total) {
                $invoice->update([
                    'paid' => $invoice->paid + $request->value,
                    "updated_at" => Carbon::now(),
                ]);
            } else {
                return response()->json(['alert_en' => 'Amount greater than final price', 'alert_ar' => 'المبلغ المستحق اكبر من السعر النهائي'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'Invoice not found', 'alert_ar' => 'الفاتورة غير موجودة'], 400);
        }
    }
}
