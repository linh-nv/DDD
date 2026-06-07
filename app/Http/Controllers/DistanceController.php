<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Testcenter\Application\Distance\CalculateDistanceCommand;

class DistanceController extends Controller
{
    public function __construct(
    ) {
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'from_lat' => ['required', 'numeric', 'between:-90,90'],
            'from_lng' => ['required', 'numeric', 'between:-180,180'],
            'to_lat' => ['required', 'numeric', 'between:-90,90'],
            'to_lng' => ['required', 'numeric', 'between:-180,180'],
            'method' => ['nullable', 'string', 'in:auto,car,bike,bird_fly'],
            'minimum_distance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $fromLat = $request->input('from_lat');
        $fromLng = $request->input('from_lng');
        $toLat = $request->input('to_lat');
        $toLng = $request->input('to_lng');
        $method = $request->input('method') ?? 'auto';
        $useCache = $request->boolean('use_cache') ?? true;
        $useMinimumDistance = $request->boolean('use_minimum_distance') ?? true;
        $minimumDistance = $request->input('minimum_distance') ?? 200;

        $result = Bus::dispatch(
            new CalculateDistanceCommand(
                fromLat: (float) $fromLat,
                fromLong: (float) $fromLng,
                toLat: (float) $toLat,
                toLong: (float) $toLng,
                method: $method,
                useCache: $useCache,
                useMinimumDistance: $useMinimumDistance,
                minimumDistance: (float) $minimumDistance,
            )
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
