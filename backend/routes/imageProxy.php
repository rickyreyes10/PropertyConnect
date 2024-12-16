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
    
    // Get image content using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $imageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($httpCode === 200 && $imageData) {
        header("Content-Type: " . $contentType);
        echo $imageData;
    } else {
        // If image fetch fails, return default image
        $defaultImage = __DIR__ . '/../../frontend/assets/default-property.jpg';
        header('Content-Type: image/jpeg');
        readfile($defaultImage);
    }
} else {
    http_response_code(400);
    die('No URL provided');
} 