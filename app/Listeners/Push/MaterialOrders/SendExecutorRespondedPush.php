<?php

namespace App\Listeners\Push\MaterialOrders;

use App\Events\Push\MaterialOrders\ExecutorResponded;
use App\FirebasePush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendExecutorRespondedPush
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
     * @param  ExecutorResponded  $event
     * @return void
     */
    public function handle(ExecutorResponded $event)
    {
        $title = 'У вас новый отклик';
        $body = 'На ваш заказ поступил новый отклик';
        Log::info(json_encode(FirebasePush::sendMessage($title, $body, $event->user)));
    }
}
