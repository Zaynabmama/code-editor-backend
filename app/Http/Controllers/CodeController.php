<?php

namespace App\Http\Controllers;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CodeController extends Controller
{
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated_data['user_id'] = Auth::id();

        $code = Code::create($validated_data);

        return response()->json([
            'code' => $code
        ], 201);
    }

    public function readCode($id)
    {
        $code = Code::find($id);

        if (!$code) {
            return response()->json(['message' => 'Code not found'], 404);
        }

        return response()->json(['code' => $code], 200);
    }

    public function index()
    {
        $codes = Code::where('user_id', Auth::id())->get();

        return response()->json(['codes' => $codes], 200);
    }

    //public function download($id)
    //{
    //    $code = Code::find($id);

    //    if (!$code) {
    //        return response()->json(['message' => 'Code not found'], 404);
    //    }

    //    $fileName = $code->title . '.py';
    //    Storage::put('codes/' . $fileName, $code->content);

    //    return response()->download(storage_path('app/codes/' . $fileName));
    //}
}
