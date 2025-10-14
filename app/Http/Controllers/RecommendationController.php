<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    private $recommendationService;

    public function __construct(SubscriptionRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function getSubscriptionRecommendation(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Utilisateur non connecté'], 401);
        }

        $recommendation = $this->recommendationService->getRecommendation($user);

        return response()->json([
            'success' => true,
            'data' => $recommendation
        ]);
    }
}