<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $picture;
    protected $heading;
    protected $message;
    protected $link;

    public function __construct($picture, $heading, $message, $link)
    {
        $this->picture = $picture;
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
            'picture' => $this->picture,
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
}
