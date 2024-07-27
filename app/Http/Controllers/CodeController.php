<?php

namespace App\Http\Controllers;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class CodeController extends Controller
{
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'title' => 'required|string|max:255',
            'code_content' => 'required|string',
        ]);

        $validated_data['user_id'] = Auth::id();

        $code = Code::create($validated_data);
        $filename = 'code_' . $code->id . '.txt';
        Storage::put('public/' . $filename, $validated_data['code_content']);


        return response()->json([
            'code' => $code,
            'file' => $filename 
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

    public function downloadCode($filename)
    {

        $filePath = storage_path('app/public/' . $filename);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath);
    }
}
