<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;

class FCMChannel
{
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toFCM($notifiable);

        if (empty($data['tokens'])) {
            return;
        }

        $messaging = app('firebase.messaging');

        $notification = FirebaseNotification::create()
            ->withTitle($data['heading'])
            ->withBody($data['message']);

        foreach ($data['tokens'] as $token) {
            try {
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification($notification)
                    ->withData([
                        'image' => $data['image'] ?? '',
                        'heading' => $data['heading'] ?? '',
                        'message' => $data['message'] ?? '',
                        'link' => is_array($data['link']) ? json_encode($data['link']) : ($data['link'] ?? ''),
                    ]);

                $messaging->send($message);

            } catch (\Exception $e) {
                Log::error("FCM Notification Failed for Token {$token}: " . $e->getMessage());
            }
        }
    }
}
