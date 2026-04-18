<?php

namespace App\Http\Controllers;

use App\Support\AltchaCaptcha;
use Illuminate\Http\JsonResponse;

class AltchaChallengeController extends Controller
{
    public function __invoke(): JsonResponse
    {
        if (! AltchaCaptcha::enabled()) {
            return response()->json([
                'message' => 'ALTCHA is not enabled.',
            ], 503);
        }

        return response()->json(AltchaCaptcha::createChallenge());
    }
}
