<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRestaurantNotification extends Notification implements ShouldQueue
{     use Queueable;

    protected $restaurant;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param  mixed  $user
     * @param  mixed  $restaurant
     * @return void
     */
    public function __construct($user, $restaurant)
    {
        $this->user = $user;
        $this->restaurant = $restaurant;
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
        return (new MailMessage)
            ->subject('New Restaurant Added')
            ->greeting('Hello ' . $this->user->name . ',')
            ->line('A new restaurant has been added: ' . $this->restaurant->name)
            ->line('Cuisine type: ' . $this->restaurant->cuisine_type)
            ->line('Contact: ' . $this->restaurant->phone)
            ->action('View Restaurant', url('/restaurants/' . $this->restaurant->location))
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