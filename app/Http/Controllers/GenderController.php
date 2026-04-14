<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GenderController extends Controller
{
    public function classify(Request $request)
    {
        $name = $request->query('name');

        // 1. Validation: Missing or Empty
        if (!$request->has('name') || $name === '') {
            return response()->json([
                "status" => "error",
                "message" => "Name parameter is required"
            ], 400);
        }

        // 2. Validation: Non-string or containing invalid characters
        if (!is_string($name) || !preg_match('/^[a-zA-Z\s\-\']+$/', $name)) {
            return response()->json([
                "status" => "error",
                "message" => "Name must be a valid string without numbers or special characters"
            ], 422);
        }

        try {
            // 3. External API Call
            $response = Http::get("https://api.genderize.io/?name={$name}");

            if ($response->failed()) {
                return response()->json(["status" => "error", "message" => "External API error"], 502);
            }

            $data = $response->json();

            // 4. Processing Logic (Handle both successful records and unknown edge cases identically)
            $sampleSize = $data['count'] ?? 0;
            $probability = $data['probability'] ?? 0;

            $isConfident = ($probability >= 0.7 && $sampleSize >= 100);

            return response()->json([
                "status" => "success",
                "data" => [
                    "name" => $name,
                    "gender" => $data['gender'] ?? null,
                    "probability" => (float) $probability,
                    "sample_size" => (int) $sampleSize,
                    "is_confident" => $isConfident,
                    "processed_at" => Carbon::now('UTC')->toIso8601String()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Internal Server Error"
            ], 500);
        }
    }
}
