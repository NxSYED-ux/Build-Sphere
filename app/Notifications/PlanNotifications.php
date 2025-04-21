<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanNotifications extends Notification implements ShouldQueue
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
        return ['database', 'fcm', 'mail'];
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

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->heading)
            ->greeting('Hello!')
            ->line($this->message)
            ->salutation('Regards,')
            ->salutation(config('app.name'));
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }

}
