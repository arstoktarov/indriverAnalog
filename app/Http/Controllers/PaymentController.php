<?php

namespace App\Http\Controllers;

use App\CloudPayments;
use App\Models\Card;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Class PaymentController
 * @package App\Http\Controllers
 */
class PaymentController extends Controller
{

    #region Card create

    public function cardCreate(Request $request)
    {
        $rules = [
            'cardCrypto' => 'required|string',
            //'token' => 'required',
            'fullname' => 'string',
        ];

        $validator = $this->validator($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = $request['user'];
        $data = array(
            'amount' => '15',
            'currency' => 'KZT',
            'user_id' => $user->id,
            'cardCrypto' => $request['cardCrypto'],
            'JsonData' => array(
                'pay_type' => 'createCard',
            ),
            'name' => $request['fullname'] ?? null,
        );
        $result = CloudPayments::payByCrypto($data);

        if ($result['Success']) {
            if (isset($result['Model']['JsonData']))
            {
                $data = [
                    '3ds' => false,
                    'html' => null,
                    'transaction_id' => $result['Model']['TransactionId'],
                ];
                $message = $this->saveCardData($result['Model']);
                $this->refundMoney($result['Model']);
                return $this->Result(200, $data, $message);
            }
            else
            {
                $data = array(
                    'TransactionId' => $result['Model']['TransactionId'],
                    'Amount' => $result['Model']['TransactionId'],
                );
                CloudPayments::refundMoney($data);
                return $this->Result(400, null, 'Error');
            }
        }
        else {
            if (isset($result['Model'])) {
                $model = $result['Model'];
                if (isset($model['AcsUrl'])) {
                    $data = array(
                        'TransactionId' => $model['TransactionId'] ?? null,
                        '3ds' => true,
                        'url' => $model['AcsUrl'] ?? null,
                        'PaReq' => $model['PaReq'] ?? null,
                    );
                    $html = view('3dsPayment', ['data' => $data])->render();
                    $returnData = array(
                        '3ds' => true,
                        'html' => $html,
                        'transaction_id' => $model['TransactionId'],
                    );
                    //return view('3dsPayment', ['data' => $data]);
                    return $this->Result(200, $returnData);
                }
                else {
                    if (isset($model['CardHolderMessage'])) {
                        $data = array(
                            'ReasonCode' => $model['ReasonCode'] ?? null,
                            'TransactionId' => $model['TransactionId'] ?? null,
                            'Reason' => $model['Reason'] ?? null,
                            'CardHolderMessage' => $model['CardHolderMessage'] ?? null,
                            'Status' => $model['Status'] ?? null,
                        );
                        return $this->Result(400, null, $data['CardHolderMessage']);
                    }
                    return $this->Result(400, null, $result['Message']);
                }
            }
            else {
                return $this->Result(400, null, $result['Message']);
            }
        }
    }

    public function processPaymentResult(Request $request)
    {
        $result = CloudPayments::post3ds($request['MD'], $request['PaRes']);
        $model = isset($result['Model']) ? $result['Model'] : null;
        if ($result['Success'])
        {
            if (isset($model['JsonData'])) {
                $message = $this->saveCardData($model);
                return response($message);
            }
            else {
                return response('Error', 400);
            }
        }
        else
        {
            if (isset($result['Model']['CardHolderMessage'])) {
                return response($result['Model']['CardHolderMessage'], 400);
            }
            return response($result['Message'], 400);
        }

        DB::table('payment_data')->insert([
            'jsondata' => $res
        ]);
        return response('Success', 200);
    }

    public function saveCardData($model)
    {
        $jsondata = json_decode($model['JsonData'], true);
        if ($jsondata['pay_type'] == 'createCard') {
            $card = Card::where('token', $model['Token'])->where('user_id', $model['AccountId'])->first();
            if ($card) return trans('payment.card.alreadyCreated');
            $card = new Card();
            $card->token = $model['Token'] ?? null;
            $card->cardFirstSix = $model['CardFirstSix'] ?? null;
            $card->cardLastFour = $model['CardLastFour'] ?? null;
            $card->cardType = $model['CardType'] ?? null;
            $card->cardTypeCode = $model['CardTypeCode'] ?? null;
            $card->fullname = $model['Name'] ?? null;
            $card->user_id = $model['AccountId'] ?? null;
            $card->save();
            return trans('messages.http_errors.200');
        }
        return trans('messages.http_errors.400');
    }

    #endregion

    #region Payment by Token

    public function payByToken(Request $request)
    {
        $user = $request['user'];
        $rules = [
            'order_id' => 'required',
            'card_id' => 'required',
        ];
        $validator = $this->validator($request->all(), $rules);
        if ($validator->fails()) return $this->Result(200, null, $validator->errors()->first());
        //$user = User::whereToken($request['Token']);
        if (!$user) return $this->Result(400, null, trans('auth.user_not_found'));
        $order = $user->orders()->find($request['order_id']);
        if (!$order) return $this->Result(400, null, trans('messages.http_errors.404-2', ['attr' => trans('messages.attributes.order')]));
        $card = $user->cards()->find($request['card_id']);
        if (!$order) return $this->Result(400, null, trans('messages.http_errors.404-2', ['attr' => trans('messages.attributes.card')]));

        $amount = $order->total_price;
        $currency = 'KZT';
        $accountId = $user->id;
        $token = $card->token ?? '';
        $result = CloudPayments::payByToken($amount, $currency, $accountId, $token);

        if ($result['Success']) {
            if (isset($result['Model'])) {
                $model = $result['Model'];
                $transaction = new Transaction();
                $transaction->setValues($model, $order->id);
                $transaction->save();
            }
            return $this->Result(200);
        }
        else {
            if (isset($result['Model'])) {
                $model = $result['Model'];
                if (isset($model['CardHolderMessage'])) {
                    $data = array(
                        'ReasonCode' => $model['ReasonCode'] ?? null,
                        'TransactionId' => $model['TransactionId'] ?? null,
                        'Reason' => $model['Reason'] ?? null,
                        'CardHolderMessage' => $model['CardHolderMessage'] ?? null,
                        'Status' => $model['Status'] ?? null,
                    );
                    return $this->Result(400, $data, $data['CardHolderMessage']);
                }
                return $this->Result(400, null, $result['Message']);
            }
            else {
                return $this->Result(400, null, $result['Message']);
            }
        }

    }

    #endregion

    public function getPublicId(Request $request) {
        $rules = [
            'code' => 'required',
        ];
        $validator = $this->validator($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());
        if ($request['code'] != User::code) return $this->Result(400, null, 'Incorrect code');
        $data = array(
            'PublicId' => CloudPayments::publicId,
        );
        return $this->Result(200, $data);
    }

    public function getPaymentData(Request $request)
    {
        $payment = DB::table('payment_data')->pluck('jsondata');
        //$payment = json_decode($payment);

        return response($payment, 200)->header('Content-Type', 'application/json');
    }

    public function checkTransaction(Request $request) {
        $rules = [
            'transaction_id' => 'required',
        ];
        $validator = $this->validator($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());
        $transaction = Transaction::where('transaction_id', $request['transaction_id'])->first();
        if (!$transaction) return $this->Result(404, null, trans('messages.http_errors.404'));
        if ($transaction->statusCode != 3 && $transaction->reasonCode != 0) {
            return $this->Result(400, null, $transaction->cardHolderMessage ?? $transaction->reason ?? null);
        }
        else {
            if ($transaction->order_id == null) {
                return $this->Result(200, null, trans('payment.card.successCreated'));
            }
            else {
                return $this->Result(200, null, $transaction->cardHolderMessage ?? $transaction->reason ?? null);
            }
        }
    }

    public function refundMoney($model) {
        $data = array(
            'TransactionId' => $model['TransactionId'],
            'Amount' => $model['Amount'],
        );
        $res = CloudPayments::refundMoney($data);
        return $res['Success'];
    }

    public static function saveTransaction($model) {
        $transaction = new Transaction();
        $transaction->setValues($model);
        $transaction->save();
    }

    public function pay(Request $request)
    {
        $rules = [
            'order_id' => 'required|numeric',
            'card_id' => '',
            'cardCrypto' => Rule::requiredIf(!is_null($request['card_id'])),
        ];
        $validator = $this->validator($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $order = Order::find($request['order_id']);
        if (!$order) return $this->Result(404, null, trans('messages.http_errors.404-2', ['attr' => trans('messages.attributes.order')]));
        //$amount =
        if ($request['card_id']) {
            $data = array(
                'amount' => $order->total_price,
                'currency' => 'KZT',
                'AccountId' => $order->user_id,
                ''
            );
        }

        //return response($res, 200)->header('Content-Type', 'application/json');
    }
}
