<?php

use Illuminate\Support\Facades\Route;

Route::get('/proxy', function () {
    $targetUrl = request()->query('url');

    if (!$targetUrl) {
        return response()->json(['error' => 'URL parameter is required'], 400);
    }

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $targetUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        
        $body = curl_exec($ch);
        
        if ($body === false) {
            throw new Exception(curl_error($ch));
        }
        
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        $filename = basename(parse_url($targetUrl, PHP_URL_PATH));
        
        return response($body)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Length', strlen($body))
            ->header('Cache-Control', 'no-cache')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
    } catch (Exception $e) {
        return response()->json([
            'error' => 'Failed to fetch the resource: ' . $e->getMessage()
        ], 500);
    }
});

Route::options('/proxy', function() {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type');
});