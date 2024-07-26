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
    public function show($id)
    {
    }
    public function listAll()
    {
        
    }
}

