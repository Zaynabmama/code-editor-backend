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

public function listAllChats()
{
    $userId = Auth::id();

    $chats = Chat::all();

    return response()->json([
        'chats' => $chats
    ], 200);

}




public function listUserChat($id)
{   
    $userId = Auth::id();
    $chats = Chat::select('chats.id', 'chats.sender_id', 'chats.receiver_id', 'chats.content')
                ->where(function ($query) use ($userId, $id) {
                    $query->where('chats.sender_id', $userId)
                          ->where('chats.receiver_id', $id);
                })
                ->get();

    return response()->json([
        'chats' => $chats,
    ], 200);
}


public function updateText(Request $request, $id)
{
    $request->validate([
        'content' => 'required|string',
    ]);

    $existingRow = Chat::findOrFail($id);
    $existingRow->text_column .= ' ' . $request->input('content');
    $existingRow->save();

    return response()->json(['message' => 'Text updated successfully!', 'data' => $existingRow], 200);
}


public function getMessagesBetweenUsers($user1_id, $user2_id)
    {
        $messages = Chat::join('users as senders', 'chats.sender_id', '=', 'senders.id')
        ->join('users as receivers', 'chats.receiver_id', '=', 'receivers.id')
        ->where(function ($query) use ($user1_id, $user2_id) {
            $query->where('chats.sender_id', $user1_id)
                ->where('chats.receiver_id', $user2_id);
        })
        ->orWhere(function ($query) use ($user1_id, $user2_id) {
            $query->where('chats.sender_id', $user2_id)
                ->where('chats.receiver_id', $user1_id);
        })
        ->select('chats.*', 'senders.name as sender_name', 'receivers.name as receiver_name')
        ->get();

    return response()->json($messages);
    }

}

