<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OlaMapsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function __construct(protected OlaMapsService $olaMapsService) {}

    /**
     * Return public map client configuration.
     */
    public function config(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->olaMapsService->getClientConfig(),
        ]);
    }

    /**
     * Autocomplete Place Search.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $input = (string) $request->input('input', '');
        $lat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $lng = $request->filled('lng') ? (float) $request->input('lng') : null;
        $limit = (int) $request->input('limit', 5);

        $results = $this->olaMapsService->autocomplete($input, $lat, $lng, $limit);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Reverse Geocode (Lat/Lng -> Address).
     */
    public function reverseGeocode(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');

        $result = $this->olaMapsService->reverseGeocode($lat, $lng);

        return response()->json([
            'success' => $result !== null,
            'data' => $result,
        ]);
    }

    /**
     * Turn-by-Turn Route / Directions Polyline.
     */
    public function directions(Request $request): JsonResponse
    {
        $request->validate([
            'origin_lat' => 'required|numeric',
            'origin_lng' => 'required|numeric',
            'dest_lat' => 'required|numeric',
            'dest_lng' => 'required|numeric',
        ]);

        $result = $this->olaMapsService->getDirections(
            (float) $request->input('origin_lat'),
            (float) $request->input('origin_lng'),
            (float) $request->input('dest_lat'),
            (float) $request->input('dest_lng')
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
