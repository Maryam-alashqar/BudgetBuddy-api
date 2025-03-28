<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SalaryTimeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
     public function via($notifiable)
    {
        return ['database', 'broadcast']; // Save in DB and send via WebSockets
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
/*     public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
/*     public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    } */ 

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
     public function toArray($notifiable)
    {
        return [
            'message' => "It's time to get your salary!",
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "It's time to get your salary!",
        ]);
    }
}
