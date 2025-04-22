<?php

namespace App\Providers;

use App\Broadcasting\FCMChannel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton('firebase.messaging', function ($app) {
            return (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->createMessaging();
        });

        $this->app->singleton(FCMChannel::class, function ($app) {
            return new FCMChannel();
        });

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('fcm', function ($app) {
                return $app->make(FCMChannel::class);
            });
        });
    }

    public function boot()
    {
        Relation::morphMap([
            'plan' => \App\Models\Plan::class,
        ]);
    }
}
