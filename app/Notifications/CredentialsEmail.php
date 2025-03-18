<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CredentialsEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subject;
    protected $email;
    protected $password;
    protected $url;

    public function __construct($subject, $email, $password, $url)
    {
        $this->subject = $subject;
        $this->email = $email;
        $this->password = $password;
        $this->url = $url;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting('Hello!')
            ->line("Your login credentials are:")
            ->line("**Email:** {$this->email}")
            ->line("**Password:** {$this->password}")
            ->action('Login Now', $this->url)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'url' => $this->url
        ];
    }
}
