<?php

namespace App\Livewire\Chat;

use App\Models\Message;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Notifications\MessageRead;
use App\Notifications\MessageSent;

class ChatBox extends Component
{
    public $selectedConversation;
    public $body;

    public $loadMessages;
    public $paginate_var = 10;



    #[On('loadMore')]
    function loadMore(): void
    {

        $this->paginate_var += 10;

        $this->loadMessages();
        $this->dispatch('update-chat-height');
    }

    public function getListeners()
    {

        $auth_id = auth()->user()->id;

        return [

            'loadMore',
            "echo-private:users.{$auth_id},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'broadcastedNotifications'

        ];
    }


    public function broadcastedNotifications($event){
        if ($event['type'] == MessageSent::class) {

            if ($event['conversation_id'] == $this->selectedConversation->id) {

                $this->dispatch('scroll-bottom');

                $newMessage = Message::find($event['message_id']);


                #push message
                $this->loadMessages->push($newMessage);

                $newMessage->read_at = now();
                $newMessage->save();

                #broadcast
                $this->selectedConversation->getReceiver()
                    ->notify(new MessageRead($this->selectedConversation->id));

            }
        }
    }

    function loadMessages()
    {
        $count = Message::where("conversation_id", $this->selectedConversation->id)->count();

        $this->loadMessages = Message::where("conversation_id", $this->selectedConversation->id)
            ->skip($count - $this->paginate_var)
            ->take($this->paginate_var)
            ->get();

        return $this->loadMessages;
    }

    public function sendMessage()
    {


        // dd($this->body);
        $this->validate(['body' => 'required|string']);
        $message = Message::create([
            "conversation_id" => $this->selectedConversation->id,
            "receiver_id" => $this->selectedConversation->getReceiver()->id,
            "sender_id" => auth()->id(),
            "body" => $this->body,
        ]);


        $this->reset(['body']);
        $this->dispatch('scroll-bottom');
        $this->loadMessages();
        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();
        $this->dispatch("refresh");


        $this->selectedConversation->getReceiver()->notify(new MessageSent(auth()->user(),$this->selectedConversation,$message,$this->selectedConversation->getReceiver()->id));
    }
    public function mount()
    {
        $this->loadMessages();
    }
    public function render()
    {

        return view('livewire.chat.chat-box');
    }
}
