<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = User::where('token', $request->header('Authorization'))->whereNotNull('phone_verified_at')->first();
        if ($user) {
            $request['user'] = $user;
            return $next($request);
        }
        else {
            $res = [
                'statusCode' => 401,
                'message' => trans('User not found'),
                'data' => null,
            ];
            return response()->json($res, $res['statusCode']);
        }
    }
}
