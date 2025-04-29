<?php

namespace App\Providers;

use App\Broadcasting\FCMChannel;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Query;
use App\Models\StaffMember;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserBuildingUnit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
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
            'plan' => Plan::class,
            'unit contract' => UserBuildingUnit::class,
            'subscription' => Subscription::class,
            'user' => User::class,
            'organization' => Organization::class,
            'staff member' => StaffMember::class,
            'query' => Query::class,
        ]);
    }
}
