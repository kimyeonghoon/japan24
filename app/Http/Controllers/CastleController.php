<?php

namespace App\Http\Controllers;

use App\Models\Castle;
use Illuminate\Http\Request;

class CastleController extends Controller
{
    public function index()
    {
        $castles = Castle::all();
        return view('castles.index', compact('castles'));
    }

    public function show(Castle $castle)
    {
        return view('castles.show', compact('castle'));
    }

    public function map()
    {
        $castles = Castle::all();
        return view('castles.map', compact('castles'));
    }
}