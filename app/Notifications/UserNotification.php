<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $image;
    protected $heading;
    protected $message;
    protected $link;

    public function __construct($image, $heading, $message, $link)
    {
        $this->image = $image;
        $this->heading = $heading;
        $this->message = $message;
        $this->link = $link;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'fcm'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'image' => $this->image,
            'heading' => $this->heading,
            'message' => $this->message,
            'link' => $this->link,
            'created_at' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toFcm($notifiable)
    {
        $serverKey = config('services.fcm.server_key');

        $fcmTokens = $notifiable->fcmTokens()->pluck('token')->toArray();

        if (empty($fcmTokens)) {
            return;
        }

        $payload = [
            'registration_ids' => $fcmTokens, // Send to multiple devices
            'notification' => [
                'title' => $this->heading,
                'body' => $this->message,
                'click_action' => $this->link,
                'image' => $this->image,
                'created_at' => now(),
            ],
            'data' => [
                'extra_info' => 'Any custom data here',
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => "key=$serverKey",
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', $payload);

        return $response->json();
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
