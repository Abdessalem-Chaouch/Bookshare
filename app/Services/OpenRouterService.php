<?php

namespace App\Services;

use GuzzleHttp\Client;

class OpenRouterService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('OPENROUTER_API_KEY');
    }

    public function getResponse($message)
    {
        $response = $this->client->post('https://openrouter.ai/api/v1/chat/completions', [
            'json' => [
                'model' => 'openai/gpt-3.5-turbo', // modèle gratuit possible
                'messages' => [
                    ['role' => 'user', 'content' => $message],
                ],
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        return $body['choices'][0]['message']['content'] ?? 'Désolé, je n’ai pas compris.';
    }
}
