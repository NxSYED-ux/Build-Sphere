<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

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
        return ['database', 'broadcast'];
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

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }

    public function toFCM($notifiable)
    {
        $fcmTokens = $notifiable->fcmTokens()->pluck('token')->toArray();

        if (empty($fcmTokens)) {
            return null;
        }

        $serviceAccountPath = storage_path('firebase_credentials.json');
        $factory = (new Factory)->withServiceAccount($serviceAccountPath);
        $messaging = $factory->createMessaging();

        $notification = FirebaseNotification::create()
            ->withTitle($this->heading)
            ->withBody($this->message);

        foreach ($fcmTokens as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification($notification)
                    ->withData([
                        'link' => $this->link,
                        'image' => $this->image
                    ]);

                $messaging->send($message);
            } catch (\Exception $e) {
                Log::error("FCM Notification Failed: " . $e->getMessage());
            }
        }
    }
}
