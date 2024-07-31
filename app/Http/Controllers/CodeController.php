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

    public function compileCode(Request $request)
{
    // Validate that 'code_content' is provided in the form data
    $validated_data = $request->validate([
        'code_content' => 'required|string',
    ]);

    $codeContent = $validated_data['code_content'];

    try {
        // Create a temporary file for Python code execution
        $tempFile = tempnam(sys_get_temp_dir(), 'code') . '.py';
        file_put_contents($tempFile, $codeContent);

        // Execute the Python code
        $process = new Process(['python', $tempFile]); // Try 'python' first
$process->run();
if (!$process->isSuccessful()) {
    // If 'python' fails, try 'python3'
    $process = new Process(['python3', $tempFile]);
    $process->run();
}

        // Capture the output and errors
        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();

        // Clean up the temporary file
        unlink($tempFile);

        if (!$process->isSuccessful()) {
            return response()->json(['error' => $errorOutput], 400);
        }

        return response()->json([
            'output' => $output // Return the actual output from Python execution
        ], 200);
    } catch (ProcessFailedException $exception) {
        return response()->json(['error' => $exception->getMessage()], 500);
    }
}

}

