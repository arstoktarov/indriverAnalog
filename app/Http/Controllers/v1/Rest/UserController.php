<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\EditProfileRequest;
use App\Models\PasswordResets;
use App\Models\User;
use App\Models\VerificationCodes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function signUp(Request $request) {
        $rules = [
            'phone' => [
                'required',
                'regex:/^[8].{10}$/m',
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = User::where('phone', $request['phone'])->whereNotNull('phone_verified_at')->first();

        if ($user) return $this->Result(400, null, 'User already exists'); //TODO add localized message

        $verification = VerificationCodes::firstOrNew($request->only('phone'));

        //$code = random_int(1000, 9999);
        $code = 1234;
        if ($verification->updated_at > Carbon::now()->subSeconds(VerificationCodes::$codeTTL)) {
            $timeRemaining = VerificationCodes::$codeTTL - Carbon::now()->diffInSeconds($verification->updated_at);
            return $this->Result(400, null, 'Невозможно отправить код, подождите ' . $timeRemaining . ' секунд');
        }
        //TODO MESSAGE SENDING PROCESS
        $verification->code = $code;
        $verification->save();


        return response()->json($verification, 200);
    }

    public function signIn(Request $request) {
        $rules = [
            'phone' => 'required',
            'password' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = User::verified()->where('phone', $request['phone'])->where('password', $request['password'])->first();

        $user->load('city');

        if (!$user) return $this->Result(401, null, trans('auth.failed')); //TODO add localized message

        return response()->json($user);
    }

    public function auth(Request $request) {
        $user = $request['user']->load('city');
        return response()->json($user);
    }

    public function createUser(CreateUserRequest $request) {
        $user = User::where('token', $request['token'])->whereNull('phone_verified_at')->first();
        if (!$user) return $this->Result(404, null, 'User not found'); // TODO add localized message

        $user->fill($request->all());
        $user->setPassword($request['password']);
        $user->verify();
        $user->save();

        $user->load('city');
        return response()->json($user);
    }

    public function resendCode(Request $request) {
        $rules = [
            'phone' => [
                'required',
                'regex:/^[8].{10}$/m',
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $verification = VerificationCodes::where('phone', $request['phone']);

        //$code = random_int(1000, 9999);
        $code = 1234;
        if ($verification->updated_at > Carbon::now()->subSeconds(VerificationCodes::$codeTTL)) {
            $timeRemaining = VerificationCodes::$codeTTL - Carbon::now()->diffInSeconds($verification->updated_at);
            return $this->Result(400, null, 'Невозможно отправить код, подождите ' . $timeRemaining . ' секунд');
        }
        //TODO MESSAGE SENDING PROCESS
        $verification->code = $code;
        $verification->save();

        return response()->json('It Works');
    }

    public function verify(Request $request) {
        $rules = [
            'phone' => [
                'required',
                'regex:/^[8].{10}$/m',
            ],
            'code' => 'required|digits:4',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors());

        $verification = VerificationCodes::where('phone', $request['phone'])
            ->where('code', $request['code'])->first();

        if (!$verification || $verification->updated_at < Carbon::now()->subSeconds(VerificationCodes::$codeTTL)) {
            if ($verification) $verification->delete();
            return $this->Result(404, null, 'Verification not found');
        }

        $user = User::firstOrNew($request->only('phone'));
        $user->phone = $request['phone'];
        $user->resetToken();
        $user->save();

        return response()->json($user->only('token'));
    }

    public function resetPassword(Request $request) {
        $rules = [
            'phone' => [
                'required',
                'regex:/^[8].{10}$/m',
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = User::where('phone', $request['phone'])->first();

        if (!$user) return $this->Result(400, null, 'User not found'); // TODO Return localized message

        $passwordReset = PasswordResets::firstOrNew($request->only('phone'));

        //$code = random_int(1000, 9999);
        $code = 1234;
        $passwordReset->phone = $request['phone'];
        $passwordReset->code = $code;
        $passwordReset->user_id = $user->id;
        $passwordReset->save();

        return response()->json(null, 200);
    }

    public function checkResetPasswordCode(Request $request) {
        $rules = [
            'phone' => [
                'required',
                'regex:/^[8].{10}$/m',
            ],
            'code' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $passwordReset = PasswordResets::where('phone', $request['phone'])->where('code', $request['code'])->first();

        if (!$passwordReset) return $this->Result(404, null, 'PasswordReset not found'); //TODO return localized message

        return response()->json($passwordReset->user->only('token'));
    }

    public function editProfile(EditProfileRequest $request) {
        $user = $request['user'];
        if ($request['name']) $user->name = $request['name'];
        if ($request['city_id']) $user->city_id = $request['city_id'];
        if ($request['password']) $user->password = $request['password'];
        $user->save();
        $user->load('city');
        return response()->json($user);
    }

}
