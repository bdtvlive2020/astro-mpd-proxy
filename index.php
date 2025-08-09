<?php
// Get MPD URL from query parameter
$mpdUrl = $_GET['url'] ?? '';

if (empty($mpdUrl)) {
    http_response_code(400);
    exit("No MPD URL provided.");
}

// Allow only MPD files for safety
if (!preg_match('/\.mpd($|\?)/', $mpdUrl)) {
    http_response_code(403);
    exit("Only MPD files are allowed.");
}

$ch = curl_init($mpdUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36"
]);
$data = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200 && $data) {
    header("Content-Type: application/dash+xml");
    echo $data;
} else {
    http_response_code($http_code ?: 500);
    echo "Error loading MPD.";
}
?>
