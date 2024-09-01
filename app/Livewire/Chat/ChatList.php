<?php

namespace App\Livewire\Chat;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatList extends Component
{
    public $selectedConversation;
    public $query;

    #[On("refresh")]
    public function render()
    {

        $user = auth()->user();
        // dd($user->conversations()->latest('updated_at')->get());
        return view('livewire.chat.chat-list',["conversations"=>$user->conversations()->latest('updated_at')->get()]);
    }
}
