<?php

namespace App\Listeners\Push\MaterialOrders;

use App\Events\Push\MaterialOrders\ExecutorDone;
use App\FirebasePush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendExecutorDonePush
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
     * @param  ExecutorDone  $event
     * @return void
     */
    public function handle(ExecutorDone $event)
    {
        $title = 'Заказ успешно выполнен!';
        $body = 'Ваш заказ успешно выполнен.';
        Log::info(json_encode(FirebasePush::sendMessage($title, $body, $event->user)));
    }
}
