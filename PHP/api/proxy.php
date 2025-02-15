<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$targetUrl = isset($_GET['url']) ? $_GET['url'] : null;

if (!$targetUrl) {
    http_response_code(400);
    echo json_encode(['error' => 'URL parameter is required']);
    exit;
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
    
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Length: " . strlen($body));
    header("Cache-Control: no-cache");
    
    echo $body;
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch the resource: ' . $e->getMessage()]);
}