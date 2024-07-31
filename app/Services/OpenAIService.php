<?php




// app/Services/OpenAIService.php

namespace App\Services;
use OpenAI\Laravel\Facades\OpenAI;
use GuzzleHttp\Client;

class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function getCompletion($prompt)
    {
        try {
            $response = $this->client->post('https://api.openai.com/v1/completions', [
                'json' => [
                    'model' => 'text-davinci-003',
                    'prompt' => $prompt,
                    'max_tokens' => 50
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Log the exception message for debugging
            //\Log::error('OpenAI API request failed: ' . $e->getMessage());
            return ['error' => 'Failed to get completion'];
        }
    }
}
