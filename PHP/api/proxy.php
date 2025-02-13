<?php

if ($_SERVER['REQUEST_URI'] == '/') {
    header('Location: /api/proxy', true, 301);
    exit;
}

if (!isset($_GET['url'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'URL parameter is required']);
    exit;
}

$url = urldecode($_GET['url']);

try {
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $header_size);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    header('Content-Type: ' . ($content_type ?: 'application/octet-stream'));
    echo $body;

    curl_close($ch);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to fetch the resource']);
}
