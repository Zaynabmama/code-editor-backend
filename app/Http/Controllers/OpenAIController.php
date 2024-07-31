<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
class OpenAIController extends Controller
{
// app/Http/Controllers/CopilotController.php

    public function getSuggestions(Request $request)
    {
        $code = $request->input('code');
        $client = new Client();
        $response = $client->post('https://api.copilot.url/suggestions', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('COPILOT_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'code' => $code,
            ],
        ]);

        return response()->json(json_decode($response->getBody(), true));
    }
}
