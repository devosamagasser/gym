<?php

namespace App\Notifications;

use Ichtrojan\Otp\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendOtp extends Notification
{
    use Queueable;
    private $type;
    /**
     * Create a new notification instance.
     */
    public function __construct($tpye)
    {
        $this->type = $tpye;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $code = (new Otp)->generate($notifiable->email,'numeric', 6)->token;
        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line("Your One-Time Password (OTP) for {$this->type} is:")
            ->line("**{$code}**")
            ->line('This OTP is valid for a limited time. Please do not share it with anyone.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
