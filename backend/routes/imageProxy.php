<?php
if (isset($_GET['url'])) {
    $imageUrl = $_GET['url'];
    
    // Validate URL
    if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        die('Invalid URL');
    }

    // Set headers to allow image loading
    header('Access-Control-Allow-Origin: *');
    
    // Get image content
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $imageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($httpCode === 200 && $imageData) {
        header("Content-Type: " . $contentType);
        echo $imageData;
    } else {
        http_response_code(404);
        die('Failed to load image');
    }
} else {
    http_response_code(400);
    die('No URL provided');
} 