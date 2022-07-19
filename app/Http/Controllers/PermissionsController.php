<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function permissions(Request $request)
    {
        $user = Users::where('token', $request->header('Authorization'))->first();
        if ($user->role == 'manager') {
            $user =  Users::where('company_id', $user->company_id)->with('branch')->get([
                'id',
                'full_name',
                'email',
                'status',
                'branch_id',
                'role',
                'phone'
            ]);
            $user = $user->map(function ($item) {
                return [
                    'id' => $item->id,
                    'full_name' => $item->full_name,
                    'email' => $item->email,
                    'phone' => $item->phone,
                    'status' => $item->status,
                    'branch' => $item->branch ? $item->branch->branch_name : null,
                    'role' => $item->role,
                ];
            });
            return $user;
        }
    }
}
