<?php

namespace App\Livewire\Chat;

use App\Models\Message;
use Livewire\Attributes\On;
use Livewire\Component;

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
