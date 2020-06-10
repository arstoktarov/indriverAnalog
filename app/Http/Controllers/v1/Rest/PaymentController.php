<?php
namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{

    public function payTest(Request $request) {
        $rules = [
            'user_id' => 'required',
            'amount' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        return redirect()->to(self::pay($request['amount'], $request['user_id']));
    }

    public function addToBalance(Request $request) {
        $rules = [
            'amount' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        return response()->json(['redirect_url' => self::pay($request['amount'], $request['user']->id)]);
    }

    static function pay($amount, $user_id)
    {
        $t = new Transaction();
        $t->user_id = $user_id;
        $t->amount = $amount;
        $t->description = 'Payment Description Here';
        $t->save();

        $arrReq = [
            'pg_merchant_id' => env('PAYBOX_ID'),
            'pg_amount' => $amount,
            'pg_salt' => Str::random(10),
            'pg_order_id' => $t->id,
            'pg_user_id' => $user_id,
            'pg_description' => $t->description,
            'pg_result_url' => route('paymentResult'),
            //'pg_success_url' => route('paymentSuccess'),
            //'pg_failure_url' => route('paymentFail'),
            'pg_result_url_method' => 'GET',
            //'pg_success_url_method' => 'GET',
            //'pg_failure_url_method' => 'GET',
            //'pg_resting_mode' => 1
        ];

        ksort($arrReq);
        array_unshift($arrReq, 'payment.php');
        array_push($arrReq, env('PAYBOX_KEY'));


        $arrReq['pg_sig'] = md5(implode(';', $arrReq));
        unset($arrReq[0], $arrReq[1]);

        //dd($arrReq);

        $url = 'https://api.paybox.money/payment.php?'. http_build_query($arrReq);

        $t->pg_salt = $arrReq['pg_salt'];
        $t->pg_sig = $arrReq['pg_sig'];
        $t->payment_url = $url;
        $t->save();

        return $url;
    }

    function paymentResult(Request $request)
    {
        Log::info(http_build_query($request->all()));
        if($request['pg_result']) {
            $transaction = Transaction::find($request['pg_order_id']);
            $arrReq = [
                'pg_merchant_id' => env('PAYBOX_ID'),
                'pg_salt' => $pg_salt = mt_rand(21, 43433)
            ];
            ksort($arrReq);
            array_unshift($arrReq, 'payment.php');
            array_push($arrReq, env('PAYBOX_KEY'));
            $pg_sig = md5(implode(';', $arrReq));

            if ($transaction){
                $user = User::find($transaction->user_id);
                $transaction->fill($request->only(Transaction::FILLABLES));
                $transaction->status = 'success';
                $transaction->payment_id = $request['pg_payment_id'];
                $user->balance += $request['pg_amount'];
                $user->save();
                $transaction->save();



                $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request>
  <pg_status>ok</pg_status>
  <pg_description>Success Payment!</pg_description>
  <pg_salt>$pg_salt</pg_salt>
  <pg_sig>$pg_sig</pg_sig>
</request>
XML;
            }
            else{

                $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request>
  <pg_status>error</pg_status>
  <pg_error_description>Not found payment</pg_error_description>
  <pg_salt>$pg_salt</pg_salt>
  <pg_sig>$pg_sig</pg_sig>
</request>
XML;
            }

            return response()->make($xml, 200)->header('Content-Type', 'application/xml');
        } else {
            return 'false';
        }
    }

    public function paymentSuccess(Request $request){
        Log::info(http_build_query($request->all()));
        return '<h2>Удачно оплачено</h2>';
    }

    public function paymentFail(){
        return '<h2>Оплата не прошла</h2>';
    }

    public function createSignature() {

    }

}



/*
 * Тестовые карты:

Имя любое латиницей


4405 6450 0000 6150    09-2025     653

5483 1850 0000 0293    09-2025     343

3775 1450 0009 951     09-2025     3446


3D cards:

4405645000006371  12-0216   292  test1

4405645000006374  12-2016   292  test1

4003035000005378  12-2025   323  secure1

5101450000007898  12-2025   454  Master1

 */
