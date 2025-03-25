<?php

namespace App\Providers;

use App\Broadcasting\FCMChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->app->singleton('userPermissions', function () {
            if (request()->user()) {
                $user = request()->user();

                $permissions = DB::select("
                SELECT perm.name
                FROM permissions perm
                LEFT JOIN userpermissions userPerm
                    ON perm.id = userPerm.permission_id AND userPerm.user_id = ?
                LEFT JOIN rolepermissions rolePerm
                    ON perm.id = rolePerm.permission_id AND rolePerm.role_id = ?
                WHERE COALESCE(userPerm.status, rolePerm.status) = 1
            ", [$user->id, $user->role_id]);

                $permissionNames = collect($permissions)->pluck('name')->toArray();

                Log::info('User Permissions:', ['permissions' => $permissionNames]);

                return $permissionNames;
            }

            return [];
        });
    }
}
