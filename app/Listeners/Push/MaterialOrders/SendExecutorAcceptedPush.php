<?php

namespace App\Listeners\Push\MaterialOrders;

use App\Events\Push\MaterialOrders\ExecutorAccepted;
use App\FirebasePush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendExecutorAcceptedPush
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
     * @param  ExecutorAccepted  $event
     * @return void
     */
    public function handle(ExecutorAccepted $event)
    {
        $title = 'Исполнитель принял ваш заказ.';
        $body = 'Исполнитель принял ваш заказ. Вы можете зайти и проверить детали.';
        Log::info(json_encode(FirebasePush::sendMessage($title, $body, $event->user)));
    }
}
