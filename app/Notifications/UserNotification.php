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
        $fcmTokens = $notifiable->fcmTokens()->pluck('token')->toArray();

        if (empty($fcmTokens)) {
            return;
        }

        // Load Firebase Credentials from JSON file
        $serviceAccount = json_decode(file_get_contents(config('firebase.credentials')), true);
        $projectId = $serviceAccount['project_id'];
        $accessToken = $this->getAccessToken($serviceAccount);

        foreach ($fcmTokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $this->heading,
                        'body' => $this->message,
                        'image' => $this->image,
                    ],
                    'data' => [
                        'click_action' => $this->link,
                        'extra_info' => 'Any custom data here',
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ])->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $payload);
        }
    }

    private function getAccessToken($serviceAccount)
    {
        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();
        $jwtPayload = base64_encode(json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ]));

        $signatureInput = "$jwtHeader.$jwtPayload";
        openssl_sign($signatureInput, $signature, file_get_contents(config('firebase.credentials')), OPENSSL_ALGO_SHA256);
        $jwt = "$signatureInput." . base64_encode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json()['access_token'] ?? null;
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
