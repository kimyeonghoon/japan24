<?php

namespace App\Http\Controllers;

use App\Models\Castle;
use Illuminate\Http\Request;

class CastleController extends Controller
{
    public function index(Request $request)
    {
        $castles = Castle::all();

        // API 요청 (JSON Accept 헤더) 처리
        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'data' => $castles->map(function ($castle) {
                    return [
                        'id' => $castle->id,
                        'name' => $castle->name,
                        'name_korean' => $castle->name_korean,
                        'prefecture' => $castle->prefecture,
                        'latitude' => $castle->latitude,
                        'longitude' => $castle->longitude,
                        'description' => $castle->description,
                        'historical_info' => $castle->historical_info,
                        'visiting_hours' => $castle->visiting_hours,
                        'entrance_fee' => $castle->entrance_fee,
                        'official_stamp_location' => $castle->official_stamp_location,
                    ];
                }),
                'total' => $castles->count()
            ]);
        }

        return view('castles.index', compact('castles'));
    }

    public function show(Castle $castle, Request $request)
    {
        // API 요청 처리
        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $castle->id,
                    'name' => $castle->name,
                    'name_korean' => $castle->name_korean,
                    'prefecture' => $castle->prefecture,
                    'latitude' => $castle->latitude,
                    'longitude' => $castle->longitude,
                    'description' => $castle->description,
                    'historical_info' => $castle->historical_info,
                    'visiting_hours' => $castle->visiting_hours,
                    'entrance_fee' => $castle->entrance_fee,
                    'official_stamp_location' => $castle->official_stamp_location,
                ]
            ]);
        }

        return view('castles.show', compact('castle'));
    }

    public function map(Request $request)
    {
        $castles = Castle::all();

        // API 요청 처리 (지도용 간소화된 데이터)
        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'data' => $castles->map(function ($castle) {
                    return [
                        'id' => $castle->id,
                        'name' => $castle->name,
                        'name_korean' => $castle->name_korean,
                        'prefecture' => $castle->prefecture,
                        'latitude' => $castle->latitude,
                        'longitude' => $castle->longitude,
                        'visiting_hours' => $castle->visiting_hours,
                        'entrance_fee' => $castle->entrance_fee,
                    ];
                }),
                'total' => $castles->count()
            ]);
        }

        return view('castles.map', compact('castles'));
    }
}