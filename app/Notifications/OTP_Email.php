<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OTP_Email extends Notification implements ShouldQueue
{
    use Queueable;

    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your OTP for Account Verification')
            ->greeting('Hello!')
            ->line('We received a request to verify your email address.')
            ->line("**Your One-Time Password (OTP) is:**")
            ->line("🔐 **{$this->otp}**")
            ->line('Please enter this code to complete your registration.')
            ->line('This OTP is valid for the next 30 minutes.')
            ->line('If you did not request this, you can safely ignore this email.')
            ->salutation('Regards,')
            ->salutation(config('app.name'));
    }


    public function toArray(object $notifiable): array
    {
        return [
            'otp' => $this->otp,
        ];
    }
}
