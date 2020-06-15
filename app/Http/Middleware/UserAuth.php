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
     * @param  String  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        $onlyAccessedType = null;
        if ($role) $onlyAccessedType = ($role == 'user') ? User::TYPE_USER : User::TYPE_EXECUTOR;

        $user = User::where('token', $request->header('Authorization'))->whereNotNull('phone_verified_at')->first();

        if (!$user) {
            $res = $this->errorMsg(trans('User not found'));
            return response()->json($res, $res['statusCode']);
        }

        if ($onlyAccessedType && $user->type != $onlyAccessedType) {
            $res = $this->errorMsg(trans('You have no permissions to access this request'));
            return response()->json($res, $res['statusCode']);
        }


        $request['user'] = $user;
        return $next($request);
    }

    function errorMsg($message = null) {
        return [
            'statusCode' => 401,
            'message' => $message,
            'data' => null,
        ];
    }
}
