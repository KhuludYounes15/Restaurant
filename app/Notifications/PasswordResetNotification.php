<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;
    protected $user;
    protected $resetPasswordToken;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $resetPasswordToken)
    {
        $this->user = $user;
        $this->resetPasswordToken = $resetPasswordToken;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Here you can customize the email body
        return (new MailMessage)
        ->subject('Password Reset Request')
        ->greeting('Hello ' . $this->user->name . ',')
        ->line('You are receiving this email because we received a password reset request for your account.')
        ->line('Your reset password token is: ' . $this->resetPasswordToken)
        ->line('If you did not request a password reset, no further action is required.')
        ->action('Reset Password', url('/reset-password?token=' . $this->resetPasswordToken))
        ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
