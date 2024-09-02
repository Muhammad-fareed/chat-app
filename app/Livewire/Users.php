<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\User;
use Livewire\Component;
use Illuminate\Broadcasting\PrivateChannel;

class Users extends Component
{

    public function message($id){
        $authId = auth()->id();
        #check conversation exists
        $conversationExists = Conversation::where(function($query) use ($authId , $id){
            $query->where("sender_id",$authId)
            ->where("receiver_id",$id);
        })->orWhere(function($query) use ($authId , $id){
            $query->where("receiver_id",$authId)
            ->where("sender_id",$id);
        })->first();

        if($conversationExists){
            return redirect()->route('chat',["query"=>$conversationExists->id]);
        }
        $createConversation = Conversation::create([
            "sender_id"=>$authId,
            "receiver_id"=>$id
        ]);
        return redirect()->route('chat',["query"=>$createConversation->id]);

    }
    public function render()
    {
        return view('livewire.users',["users"=>User::where("id","!=",auth()->id())->get()]);
    }
}
