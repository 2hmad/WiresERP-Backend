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
            return response()->json(['alert_en' => 'Unauthorized', 'alert_ar' => 'غير مصرح'], 401);
        } else {
            $token = $request->header('Authorization');
            $user = Users::where('token', $token)->first();
            if ($user === null) {
                return response()->json(['alert_en' => 'Unauthorized', 'alert_ar' => 'غير مصرح'], 401);
            } else {
                return $next($request);
            }
        }
    }
}
