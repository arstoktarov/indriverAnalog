<?php

namespace App\Listeners\Push\MaterialOrders;

use App\Events\Push\MaterialOrders\UserAcceptedResponse;
use App\FirebasePush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendUserAcceptedResponsePush
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserAcceptedResponse  $event
     * @return void
     */
    public function handle(UserAcceptedResponse $event)
    {
        $title = 'Пользователь принял ваш отклик';
        $body = 'Пользователь принял ваш отклик. Вы можете начинать заказ!';
        Log::info(json_encode(FirebasePush::sendMessage($title, $body, $event->user)));
    }
}
