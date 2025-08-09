<?php
// যদি সরাসরি পুরো MPD URL পাঠানো হয়
$get = $_GET['url'] ?? '';
if (empty($get)) {
    http_response_code(400);
    exit("No URL provided.");
}

// সুরক্ষার জন্য কেবল MPD অনুমতি দেব
if (!preg_match('/\.mpd$/', $get)) {
    http_response_code(403);
    exit("Only MPD files allowed.");
}

$ch = curl_init($get);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36"
]);
$data = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    header("Content-Type: application/dash+xml");
    echo $data;
} else {
    http_response_code($http_code);
    echo "Error loading MPD.";
}
?>
