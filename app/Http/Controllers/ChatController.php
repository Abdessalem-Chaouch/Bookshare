<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenRouterService;

class ChatController extends Controller
{
    protected $openRouterService;

    public function __construct(OpenRouterService $openRouterService)
    {
        $this->openRouterService = $openRouterService;
    }

    public function handleRequest(Request $request)
    {
        $message = $request->input('message');
        $response = $this->openRouterService->getResponse($message);
        return response()->json(['response' => $response]);
    }
}
