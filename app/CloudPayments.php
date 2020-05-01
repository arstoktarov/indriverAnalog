<?php


namespace App;


use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Request;

class CloudPayments
{
    const baseUrl = 'https://api.cloudpayments.kz';
    const publicId = 'asdasd';
    const password = 'asdasd';
    const paymentUrl = 'https://api.cloudpayments.kz/payments/cards/charge';
    const payByTokenUrl = 'https://api.cloudpayments.kz/payments/tokens/charge';
    const payByCryptoUrl = 'https://api.cloudpayments.kz/payments/cards/charge';
    const refundMoneyUrl = 'https://api.cloudpayments.kz/payments/refund';


    public static function payByCrypto($data)
    {
        $ch = curl_init();
        $url = self::paymentUrl;
        $headers = array(
            'Content-Type: application/json'
        );
        $fields = array(
            'Amount' => $data['amount'],
            'Currency' => $data['currency'],
            'AccountId' => $data['user_id'],
            'CardCryptogramPacket' => $data['cardCrypto'],
            'JsonData' => $data['JsonData'],
            'Name' => $data['name'] ?? null,
        );
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, self::publicId.':'.self::password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        $res = json_decode($result, true);
        self::saveTransaction($res);
        return $res;
    }

    public static function payByToken($amount, $currency, $accountId, $token, $orderId)
    {
        $ch = curl_init();
        $url = 'https://api.cloudpayments.kz/payments/tokens/charge';
        $headers = array(
            'Content-Type: application/json'
        );
        $fields = array(
            'Amount' => $amount,
            'Currency' => $currency,
            'InvoiceId' => $orderId,
            'AccountId' => $accountId,
            'Token' => $token,
        );
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, self::publicId.':'.self::password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        $res = json_decode($result, true);
        self::saveTransaction($res);
        return $res;
    }

    public static function refundMoney($data)
    {
        $ch = curl_init();
        $url = 'https://api.cloudpayments.kz/payments/refund';
        $headers = array(
            'Content-Type: application/json'
        );
        $fields = array(
            'TransactionId' => $data['TransactionId'],
            'Amount' => $data['Amount'],
        );
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, self::publicId.':'.self::password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        $res = json_decode($result, true);
        return $res;
    }

    public static function post3ds($md, $PaRes)
    {
        $ch = curl_init();
        $url = 'https://api.cloudpayments.kz/payments/cards/post3ds';
        $headers = array(
            'Content-Type: application/json'
        );
        $fields = array(
            'TransactionId' => $md,
            'PaRes' => $PaRes,
        );

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, self::publicId.':'.self::password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        $res = json_decode($result, true);
        self::saveTransaction($res);
        return $res;
    }

    public static function saveTransaction($result)
    {
        if (isset($result['Model'])) {
            $model = $result['Model'];
            if (!isset($model['AcsUrl'])) {
                $transaction = new Transaction();
                $transaction->setValues($model);
                $transaction->save();
            }
        }
    }




    public static function sendTestMessage()
    {
        $ch = curl_init();
        $url = self::baseUrl.'/test';
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, self::publicId.':'.self::password);

        $result = curl_exec($ch);
        return $result;
    }
    public static function get3dsSite($data) {
        $ch = curl_init();
        $url = $data['url'];
        /*        $headers = array(
                    'Content-Type: application/json'
                );*/
        $fields = array(
            'MD' => $data['TransactionId'],
            'PaReq' => $data['PaReq'],
            'TermUrl' => 'http://194.4.58.28:9999/api/payment/process',
        );
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, self::publicId.':'.self::password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        return $result;
    }
    public static function getTokensList()
    {
        $url = 'https://api.cloudpayments.kz/payments/tokens/list';
    }
    public static function checkTransaction() {

    }
    public static function sendTestCardMessage()
    {
        $ch = curl_init();
        $url = self::paymentUrl;
        $headers = array(
            'Content-Type: application/json'
        );
        $fields = array(
            'Amount' => '1000',
            'Currency' => 'KZT',
            //'IpAddress' => '194.4.58.28',
            'name' => 'Name Surname',
            'CardCryptogramPacket' => '014111111111191202eXm5pEsVvje9tRnQN/Ia66m7djZekTD/MEghZAv0VIQ9KDVNuwqFTIxyObcoVFrSXlQtmtya9M6wzweLeRYyXFw+1V6ae4EtS1hpXdq8+aBY7IPK1FDKbekTl0FeRlRsLrxxysAD/+Ys3pXdiYrTzMX4MhQ79uATRGQMVbnE38oTbhqkis7loQSIiDUyHbIFL8iwLAJFmbcErXOZ9MfQXolhBDJCeHux/uKcTTCDu7mEkLdoTzN7fJ3I1mI/yANvb/SVY4FFUeX8gJo1r0mRd5N0utGPE4YESuI02nPhPlTajFauYFbLaV2c59D76mqgQMgNYHrYGqaK3rHD4OiKRQ=='
        );
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, self::publicId.':'.self::password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));



        $result = curl_exec($ch);
        return $result;
    }



}