<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Client;

class AIController extends Controller
{
    public function generateDescription(Request $request)
    {
        $request->validate([
            'keywords' => 'required|string|max:255'
        ]);

        $keywords = $request->input('keywords');

        try {
            $client = new Client(env('OPENAI_API_KEY'));

            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => "Write a short, catchy, and friendly store description for: $keywords"]
                ]
            ]);

            $description = $response->choices[0]->message->content ?? 'AI could not generate text 😅';

            return response()->json(['description' => $description]);

        } catch (\Exception $e) {
            // For debugging, you can log the actual error
            \Log::error('AI Error: '.$e->getMessage());
            return response()->json(['description' => 'AI failed to generate text 😅'], 500);
        }
    }
}
