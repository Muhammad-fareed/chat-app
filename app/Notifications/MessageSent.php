<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class MessageSent extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $user;
    public $conversation;
    public $message;
    public $receiverId;

    public function __construct($user, $conversation, $message, $receiverId)
    {
        $this->user = $user;
        $this->conversation = $conversation;
        $this->message = $message;
        $this->receiverId = $receiverId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    /**
     * Get the data to broadcast.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'message_id' => $this->message->id,
            'receiver_id' => $this->receiverId,
        ]);
    }

    /**
     * Determine the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        // Log the broadcast channel for debugging

        return [

            new PrivateChannel('users.' . $this->receiverId),

        ];

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'conversation_id' => $this->conversation->id,
            'message_id' => $this->message->id,
            'receiver_id' => $this->receiverId,
        ];
    }
}
