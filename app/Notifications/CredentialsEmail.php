<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CredentialsEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $name;
    protected $email;
    protected $password;

    public function __construct($name, $email, $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Credentials Email')
            ->markdown('emails.credentials', [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}
