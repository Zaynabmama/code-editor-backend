<?php

namespace App\Http\Controllers;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required',
            'content' => 'required|string',
        ]);

        $validated_data['sender_id'] = Auth::id();

        $chat = Chat::create($validated_data);

        
        return response()->json(['message' => $chat], 201);
    }
    //public function show($id)
//{
  //  $chat = Chat::find($id);

   // if (!$chat) {
   //     return response()->json(['message' => 'Chat not found'], 404);
   // }

   // return response()->json(['chat' => $chat], 200);
//}

public function show($id)
{
    $chat = Chat::join('users as senders', 'chats.sender_id', '=', 'senders.id')
        ->join('users as receivers', 'chats.receiver_id', '=', 'receivers.id')
        ->select(
            'chats.id',
            'chats.content',
            'senders.id as sender_id',
            'senders.name as sender_name',
            'receivers.id as receiver_id',
            'receivers.name as receiver_name'
        )
        ->where('chats.id', $id)
        ->first();

    if (!$chat) {
        return response()->json(['message' => 'Chat not found'], 404);
    }

    return response()->json([
        'chat' => $chat
    ], 200);
}

public function listAll()
{
    $userId = Auth::id();

    $chats = Chat::join('users as senders', 'chats.sender_id', '=', 'senders.id')
        ->join('users as receivers', 'chats.receiver_id', '=', 'receivers.id')
        ->select(
            'chats.id',
            'chats.content',
            'senders.id as sender_id',
            'senders.name as sender_name',
            'receivers.id as receiver_id',
            'receivers.name as receiver_name',
            'chats.created_at'
        )
        ->where('chats.sender_id', $userId)
        ->orWhere('chats.receiver_id', $userId)
    
        ->get();

    return response()->json([
        'chats' => $chats
    ], 200);

}

}

