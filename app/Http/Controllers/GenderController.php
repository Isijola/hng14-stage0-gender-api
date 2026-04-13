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

        // 2. Validation: Non-string (Though query params are usually strings, 
        // we check if it's numeric/array to satisfy the 422 requirement)
        if (!is_string($name) || is_numeric($name)) {
            return response()->json([
                "status" => "error",
                "message" => "Name must be a valid string"
            ], 422);
        }

        try {
            // 3. External API Call
            $response = Http::get("https://api.genderize.io/?name={$name}");

            if ($response->failed()) {
                return response()->json(["status" => "error", "message" => "External API error"], 502);
            }

            $data = $response->json();

            // 4. Edge Cases (null gender or 0 count)
            if (is_null($data['gender']) || $data['count'] === 0) {
                return response()->json([
                    "status" => "error",
                    "message" => "No prediction available for the provided name"
                ], 200); // Note: Task says return this JSON; usually 200 or 404. Let's stick to the JSON structure provided.
            }

            // 5. Processing Logic
            $sampleSize = $data['count'];
            $probability = $data['probability'];

            $isConfident = ($probability >= 0.7 && $sampleSize >= 100);

            return response()->json([
                "status" => "success",
                "data" => [
                    "name" => $name,
                    "gender" => $data['gender'],
                    "probability" => $probability,
                    "sample_size" => $sampleSize,
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
