<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use App\Models\Products;
use App\Models\SaleBillElements;
use App\Models\SaleBillExtra;
use App\Models\SaleBillReturns;
use App\Models\SaleBills;
use App\Models\Users;
use App\Models\Warehouses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleBillController extends Controller
{
    public function saleBill(Request $request)
    {
        // Get sale bills by company id with relationships 
        $user = Users::where('token', $request->header('Authorization'))->first();
        $saleBills = SaleBills::where('company_id', $user->company_id)->with('element', 'extra')->orderBy('id', 'DESC')->get();
        $saleBills = $saleBills->map(function ($item) {
            return [
                "id" => $item->id,
                "client_id" => $item->client_id,
                "client_name" => Clients::find($item->client_id)->c_name,
                "bill_number" => $item->bill_number,
                "date_time" => $item->date_time,
                "warehouse_id" => $item->warehouse_id,
                "warehouse_name" => Warehouses::find($item->warehouse_id)->warehouse_name,
                "value_added_tax" => $item->value_added_tax,
                "final_total" => $item->final_total,
                "paid" => $item->paid,
                "status" => $item->status,
                "products" => $item->element->map(function ($item) {
                    return [
                        "id" => $item->id,
                        "product_id" => $item->product_id,
                        "product_name" => Products::find($item->product_id)->product_name,
                        "product_price" => $item->product_price,
                        "quantity" => $item->quantity,
                        "unit" => $item->unit,
                        "quantity_price" => $item->quantity_price,
                        "final_total" => $item->final_total,
                    ];
                }),
                "extras" => $item->extra->map(function ($item) {
                    return [
                        "action" => $item->action,
                        "action_type" => $item->action_type,
                        "value" => $item->value,
                    ];
                }),
            ];
        });
        return $saleBills;
    }
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
                    'final_total' => $request->final_total,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
                // Decrease from product in stock
                $product->warehouse_balance = $product->warehouse_balance - $request->quantity;
                $product->save();
                return response()->json(['id' => $sale_bill->id], 200);
            }
        } else {
            return response()->json(['alert_en' => 'Client or Warehouse or Product not found', 'alert_ar' => '???????????? ???? ???????????? ???? ???????????? ?????? ??????????'], 400);
        }
    }
    public function deleteSaleBill(Request $request, $bill_id)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $sale_bill = SaleBills::where([
            ['company_id', $user->company_id],
            ['id', $bill_id]
        ])->first();
        if ($sale_bill) {
            $sale_bill->delete();
            $elements = SaleBillElements::where([
                ['company_id', $user->company_id],
                ['sale_bill_id', $bill_id]
            ])->get();
            foreach ($elements as $element) {
                $product = Products::where([
                    ['company_id', $user->company_id],
                    ['id', $element->product_id]
                ])->first();
                $product->warehouse_balance = $product->warehouse_balance + $element->quantity;
                $product->save();
            }
            SaleBillElements::where('sale_bill_id', $bill_id)->delete();
            SaleBillExtra::where('sale_bill_id', $bill_id)->delete();
        } else {
            return response()->json(['alert_en' => 'Sale Bill not found', 'alert_ar' => '???????????????? ?????? ????????????'], 400);
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
                $sale_bill->final_total = $sale_bill->final_total + $request->final_total;
                $sale_bill->save();
                SaleBillElements::create([
                    'company_id' => $user->company_id,
                    'sale_bill_id' => $request->sale_bill_id,
                    'product_id' => $request->product_id,
                    'product_price' => $request->product_price,
                    'quantity' => $request->quantity,
                    'unit' => $request->unit,
                    'quantity_price' => $request->quantity_price,
                    'final_total' => $request->final_total,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
                $product->warehouse_balance = $product->warehouse_balance - $request->quantity;
                $product->save();
            } else {
                return response()->json(['alert_en' => 'Sale bill or Product not found', 'alert_ar' => '???????????????? ???? ???????????? ?????? ??????????'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'Product already added to this sale bill', 'alert_ar' => '???????????? ?????????? ???????????? ???? ?????? ????????????????'], 400);
        }
    }
    public function deleteProductSaleBill(Request $request, $bill_id, $product_id)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $sale_bill = SaleBills::where([
            ['company_id', $user->company_id],
            ['id', $bill_id]
        ])->first();
        $product = Products::where([
            ['company_id', $user->company_id],
            ['id', $product_id]
        ])->first();
        $checkElement = SaleBillElements::where([
            ['company_id', $user->company_id],
            ['sale_bill_id', $bill_id],
            ['product_id', $product_id]
        ])->first();
        if ($checkElement !== null) {
            if ($sale_bill !== null && $product !== null) {
                $product->warehouse_balance = $product->warehouse_balance + $checkElement->quantity;
                $product->save();
                $sale_bill->final_total = $sale_bill->final_total - $checkElement->final_total;
                $sale_bill->save();
                $checkElement->delete();
            } else {
                return response()->json(['alert_en' => 'Sale bill or Product not found', 'alert_ar' => '???????????????? ???? ???????????? ?????? ??????????'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'Product not found in this sale bill', 'alert_ar' => '???????????? ?????? ?????????? ???? ?????? ????????????????'], 400);
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
            return response()->json(['alert_en' => 'Invoice not found', 'alert_ar' => '???????????????? ?????? ????????????'], 400);
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
            $extra_bill = SaleBillExtra::where([
                ['company_id', $user->company_id],
                ['sale_bill_id', $invoice->id]
            ])->get();
            $shipping_amount = 0;
            $discount_amount = 0;
            foreach ($extra_bill as $extra) {
                if ($extra->action == 'shipping') {
                    if ($extra->action_type == 'percentage') {
                        $shipping_amount = $invoice->total * $extra->value / 100;
                    } else {
                        $shipping_amount = $extra->value;
                    }
                }
                if ($extra->action == 'total') {
                    if ($extra->action_type == 'percentage') {
                        $discount_amount = $invoice->total * $extra->value / 100;
                    } else {
                        $discount_amount = $extra->value;
                    }
                }
            }
            if ($request->value <= (($invoice->final_total + $shipping_amount) - $discount_amount)) {
                $invoice->update([
                    'paid' => $request->value,
                    "updated_at" => Carbon::now(),
                ]);
            } else {
                return response()->json(['alert_en' => 'Amount greater than final price', 'alert_ar' => '???????????? ?????????????? ???????? ???? ?????????? ??????????????'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'Invoice not found', 'alert_ar' => '???????????????? ?????? ????????????'], 400);
        }
    }
    public function returnInvoice(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $invoices = SaleBillReturns::where('company_id', $user->company_id)->orderBy('id', 'DESC')->get();
        $invoices = $invoices->map(function ($item) {
            return [
                "id" => $item->id,
                "bill_id" => $item->sale_bill_id,
                "product_id" => $item->product_id,
                "product_name" => Products::find($item->product_id) ? Products::find($item->product_id)->product_name : null,
                "client_id" => $item->client_id,
                "client_name" => Clients::find($item->client_id) ? Clients::find($item->client_id)->c_name : null,
                "quantity" => $item->quantity,
                "date_time" => $item->date_time,
                "notes" => $item->notes,
            ];
        });
        return $invoices;
    }
    public function addReturnInvoice(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        $invoice = SaleBills::where([
            ['company_id', $user->company_id],
            ['id', $request->id]
        ])->first();
        if ($invoice !== null) {
            $elements = SaleBillElements::where([
                ['company_id', $user->company_id],
                ['sale_bill_id', $invoice->id],
                ['product_id', $request->product_id]
            ])->first();
            if ($elements !== null) {
                if ($elements->quantity >= $request->quantity) {
                    $elements->update([
                        'quantity' => $elements->quantity - $request->quantity,
                        "updated_at" => Carbon::now(),
                    ]);
                    $invoice->update([
                        'status' => 'return',
                        "updated_at" => Carbon::now(),
                    ]);
                    SaleBillReturns::create([
                        'company_id' => $user->company_id,
                        'sale_bill_id' => $invoice->id,
                        'product_id' => $request->product_id,
                        'client_id' => $invoice->client_id,
                        'quantity' => $request->quantity,
                        'date_time' => Carbon::parse($request->date_time)->format('Y-m-d H:i:s'),
                        'notes' => $request->notes,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ]);
                } else {
                    return response()->json(['alert_en' => 'Quantity greater than quantity in invoice', 'alert_ar' => '???????????? ???????????????? ???????? ???? ???????????? ???????????????? ???? ????????????????'], 400);
                }
            } else {
                return response()->json(['alert_en' => 'Product not found in this invoice', 'alert_ar' => '???????????? ?????? ?????????? ???? ?????? ????????????????'], 400);
            }
        } else {
            return response()->json(['alert_en' => 'Invoice not found', 'alert_ar' => '???????????????? ?????? ????????????'], 400);
        }
    }
}
