<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendWelcomeEmail extends Notification
{
    use Queueable;

    private $details;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->details = $details;
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
        $mailMessage = new MailMessage;
        $mailMessage->introLines = [
            'Welcome to the AlumniMatch Community! You are now a member of our trusted, invite-only community of college alumni networks from around the world. You are a member of only your alumni network and you know when you post on AlumniMatch that your information is only visible to other alumni from your college or colleges. When joining you are instantly matched with posts from the Bulletin Board and with other alumni like you. We can\'t wait to see how you\'ll use AlumniMatch!',
        ];
        $mailMessage->outroLines = [
            // 'User must copy and paste the URL below into their web browser'
        ];

        $mailMessage->greeting('Hi '.$notifiable->first_name.' '.$notifiable->last_name)
            ->subject('Welcome to AlumniMatch - Build your connections today!')
            ->line('Imagine who you will meet on AlumniMatch! Old connections, new bonds - it\'s all waiting for you on the app!')
            ->line('Please participate on the Bulletin Board and be a good digital citizen within your virtual alumni community on AlumniMatch. Thank you, AlumniMatch Team - learn more at www.alumnimatch.co - or find us on social media, @AlumniMatch.');
        return $mailMessage;
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
            'offer_id' => 1
        ];
    }
}
