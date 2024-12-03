<?php
header("Access-Control-Allow-Origin: *");

$url = $_GET['url'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    // Handle error
    die('Error reading from the OpenRouteService API: ' . curl_error($ch));
}

curl_close($ch);

echo $response;
?>
