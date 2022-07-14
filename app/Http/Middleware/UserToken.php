<?php

namespace App\Http\Middleware;

use App\Models\Users;
use Closure;
use Illuminate\Http\Request;

class UserToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Authorization') === null) {
            return response()->json(['alert_en' => 'Authorization is required', 'alert_ar' => 'سجل دخولك اولاً'], 400);
        } else {
            $token = $request->header('Authorization');
            $user = Users::where('token', $token)->first();
            if ($user === null) {
                return response()->json(['alert_en' => 'User not found', 'alert_ar' => 'المستخدم غير موجود'], 400);
            } else {
                return $next($request);
            }
        }
    }
}
