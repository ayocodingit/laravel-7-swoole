<?php

namespace App\Providers;

use App\Events\GenerateLetterSampleEvent;
use App\Events\GenerateLetterSwabAntigenEvent;
use App\Events\SampleValidatedEvent;
use App\Listeners\CreateAntigenSwabLetterListener;
use App\Listeners\CreateSampleListenerLetterListener;
use App\Listeners\PublishTopicMessageSampleValidatedListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        SampleValidatedEvent::class => [
            PublishTopicMessageSampleValidatedListener::class
        ],
        GenerateLetterSwabAntigenEvent::class => [
            CreateAntigenSwabLetterListener::class
        ],
        GenerateLetterSampleEvent::class => [
            CreateSampleListenerLetterListener::class,
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
