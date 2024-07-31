<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;

class OpenAIController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function generateCompletion(Request $request)
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
        ]);

        try {
            $response = $this->openAIService->getCompletion($validated['prompt']);
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error generating completion: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get completion'], 500);
        }
    }
}
