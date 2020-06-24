<?php

namespace App\Http\Controllers\v1\Rest;

use App\FirebasePush;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\EditProfileRequest;
use App\Http\Resources\AuthorizedUserResource;
use App\Models\PasswordResets;
use App\Models\User;
use App\Models\VerificationCodes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
include_once "smsc_api.php";

class UserController extends Controller
{
    const REGISTER_MSG = 'Код доступа: %d, Спец. Техника';
    const contacts = '   С уважением, \'Спец техника\'';

    #region Registration

    public function signUp(Request $request) {
        $rules = [
            'phone' => [
                'required',
                'regex:/^[8].{10}$/m',
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = User::firstOrNew($request->only('phone'));
        $user->phone = $request['phone'];

        if ($user->verified) return $this->Result(400, null, 'User already verified'); //TODO add localized message

        //$code = random_int(1000, 9999);
        $code = 1234;
        if ($user->updated_at > Carbon::now()->subSeconds(VerificationCodes::$codeTTL)) {
            $timeRemaining = VerificationCodes::$codeTTL - Carbon::now()->diffInSeconds($user->updated_at);
            return $this->Result(400, null, 'Невозможно отправить код, подождите ' . $timeRemaining . ' секунд');
        }

        $msg = 'KazIndriver: '.$code;
        $smsResult = send_sms($request['phone'], $msg, 1);
        if ($smsResult[1] <= 0) {
            return $this->Result(500, null, 'Can\'t send message, sorry. Please try again later!');
        }

        $user->phone_verification_code = $code;
        $user->touch();
        $user->save();

        return response()->json($user->only('phone'), 200);
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

        $user = User::where('phone', $request['phone']);

        if (!$user->verified) return $this->Result(400, null, 'User already verified'); //TODO add localized message
        //$code = random_int(1000, 9999);
        $code = 1234;
        if ($user->updated_at > Carbon::now()->subSeconds(VerificationCodes::$codeTTL)) {
            $timeRemaining = VerificationCodes::$codeTTL - Carbon::now()->diffInSeconds($user->updated_at);
            return $this->Result(400, null, 'Невозможно отправить код, подождите ' . $timeRemaining . ' секунд');
        }
        //TODO MESSAGE SENDING PROCESS
        $user->code = $code;
        $user->save();

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

        $user = User::where('phone', $request['phone'])
            ->where('phone_verification_code', $request['code'])->whereNull('phone_verified_at')->first();

        if (!$user || $user->updated_at < Carbon::now()->subSeconds(VerificationCodes::$codeTTL)) {
            return $this->Result(404, null, 'Verification not found');
        }

        $user->resetToken();
        $user->save();

        return response()->json($user->only('token'));
    }

    #endregion

    public function signIn(Request $request) {
        $rules = [
            'phone' => 'required',
            'password' => 'required',
            'device_token' => 'string',
            'device_type' => 'string|in:android,ios',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = User::verified()->where('phone', $request['phone'])->where('password', $request['password'])->first();

        if (!$user) return $this->Result(401, null, trans('auth.failed')); //TODO add localized message

        if ($request['device_token']) $user->device_token = $request['device_token'];
        if ($request['device_type']) $user->device_type = $request['device_type'];
        $user->save();

        $user->load('city');

        return response()->json(new AuthorizedUserResource($user));
    }

    public function auth(Request $request) {
        $user = $request['user']->load('city');
        if ($request['device_token']) $user->device_token = $request['device_token'];
        if ($request['device_type']) $user->device_type = $request['device_type'];
        $user->save();
        return response()->json(new AuthorizedUserResource($user));
    }

    public function createUser(CreateUserRequest $request) {
        $user = User::where('token', $request['token'])->whereNull('phone_verified_at')->first();
        if (!$user) return $this->Result(404, null, 'User not found'); // TODO add localized message

        $user->fill($request->all());
        $user->setPassword($request['password']);
        $user->verify();
        $user->save();

        $user->load('city');
        return response()->json(new AuthorizedUserResource($user));
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

        $user->fill($request->all());
        if ($request['password']) $user->password = $request['password'];

        $user->save();
        $user->load('city');
        return response()->json(new AuthorizedUserResource($user));
    }

    public function changeType(Request $request) {
        $rules = [
            'type' => 'required|in:1,2'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = $request['user'];
        $user->type = $request['type'];
        $user->save();

        return response()->json(new AuthorizedUserResource($user));
    }

    public function changePassword(Request $request) {
        $rules = [
            'token' => 'required',
            'password' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = User::where('token', $request['token'])->first();
        if (!$user) return $this->Result(401, null, 'User not found');

        $user->setPassword($request['password']);

        $user->save();

        return response()->json(new AuthorizedUserResource($user));
    }

    public function sendPush(Request $request) {
        $rules = [
            'title' => 'required',
            'body' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors());

        return FirebasePush::sendMessage($request['title'], $request['body'], $request['user']);
    }
}
