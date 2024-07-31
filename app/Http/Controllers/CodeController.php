<?php

namespace App\Http\Controllers;
use App\Models\Code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CodeController extends Controller
{
    // public function store(Request $request)
    // {
    //     $validated_data = $request->validate([
          
    //         'code_content' => 'required|string',
    //     ]);

    //     $validated_data['user_id'] = Auth::id();

    //     $code = Code::create($validated_data);
    //     $filename = 'code_' . $code->id . '.txt';
    //     Storage::put('public/' . $filename, $validated_data['code_content']);


    //     return response()->json([
    //         'code' => $code,
    //         'file' => $filename 
    //     ], 201);  
       
    
    // }
    public function store(Request $request)
{

    $validated_data = $request->validate([
        'code_content' => 'required|string',
   
    'title' => 'nullable|string',
]);

$validated_data['user_id'] = Auth::id();


if (!isset($validated_data['title'])) {
    $validated_data['title'] = 'Untitled';
}

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

    public function compileCode(Request $request)
{
    $validated_data = $request->validate([
        'code_content' => 'required|string',
    ]);

    $codeContent = $validated_data['code_content'];

    try {

        $tempFile = tempnam(sys_get_temp_dir(), 'code') . '.py';
        file_put_contents($tempFile, $codeContent);

       
        $process = new Process(['python', $tempFile]);
$process->run();


        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();

        unlink($tempFile);

        if (!$process->isSuccessful()) {
            return response()->json(['error' => $errorOutput], 400);
        }

        return response()->json([
            'output' => $output
        ], 200);
    }     
    
    
    catch (ProcessFailedException $exception) {
        return response()->json(['error' => $exception->getMessage()], 500);
    }
}

}

