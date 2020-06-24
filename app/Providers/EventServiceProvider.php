<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\Push\MaterialOrders\ExecutorResponded' => [
            'App\Listeners\Push\MaterialOrders\SendExecutorRespondedPush'
        ],
        'App\Events\Push\MaterialOrders\ExecutorAccepted' => [
            'App\Listeners\Push\MaterialOrders\SendExecutorAcceptedPush'
        ],
        'App\Events\Push\MaterialOrders\ExecutorDone' => [
            'App\Listeners\Push\MaterialOrders\SendExecutorDonePush'
        ],
        'App\Events\Push\MaterialOrders\UserAcceptedResponse' => [
            'App\Listeners\Push\MaterialOrders\SendUserAcceptedResponsePush'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
