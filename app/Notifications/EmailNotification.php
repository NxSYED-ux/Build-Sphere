<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailNotification extends Notification implements ShouldQueue
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
        return ['database', 'mail'];
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

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->heading)
            ->line($this->message)
            ->action('View Details', url($this->link['web'] ?? $this->link));
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }

}
