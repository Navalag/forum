<?php

namespace App\Notifications;

use App\Reply;
use App\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThreadWasUpdated extends Notification
{
    use Queueable;

    /**
     * The thread that was updated.
     *
     * @var Thread
     */
    protected $thread;

    /**
     * The new reply.
     *
     * @var Reply
     */
    protected $reply;

    /**
     * Create a new notification instance.
     *
     * @param $thread
     * @param $reply
     */
    public function __construct($thread, $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $url = $this->reply->path();

        return (new MailMessage)
            ->subject('Thread received reply')
            ->action($this->reply->owner->name .' replied to ' .$this->thread->title , $url)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->reply->owner->name . ' replied to ' . $this->thread->title,
            'link'    => $this->reply->path()
        ];
    }
}
