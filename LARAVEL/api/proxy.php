<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('proxy');
});

Route::get('/api/proxy', function (Request $request) {
    $url = $request->query('url');

    if (!$url) {
        return response()->json(['error' => 'URL parameter is required'], 400);
    }

    try {
        $response = Http::get(urldecode($url));

        $contentType = $response->header('Content-Type') ?: 'application/octet-stream';

        return response($response->body(), 200)
            ->header('Content-Type', $contentType)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch the resource'], 500);
    }
})->name('proxy');
