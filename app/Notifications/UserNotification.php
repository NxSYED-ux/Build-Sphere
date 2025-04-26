<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $image;
    protected $heading;
    protected $message;
    protected $link;

    public function __construct($image, $heading, $message, $link)
    {
        $this->image = $image ?? 'uploads/Notification/Light-theme-Logo.svg';
        $this->heading = $heading;
        $this->message = $message;
        $this->link = $link;
    }

    public function via($notifiable)
    {
        return ['database', 'fcm'];
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
        return [
            'tokens' => $notifiable->fcmTokens()->pluck('token')->toArray(),
            'heading' => $this->heading,
            'message' => $this->message,
            'link' => $this->link,
            'image' => $this->image,
        ];
    }

}
