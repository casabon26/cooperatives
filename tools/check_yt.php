<?php
// Simple script to query YouTube Data API for a given video id passed as first arg
$id = $argv[1] ?? null;
$key = $argv[2] ?? null;
if (!$id || !$key) {
    echo "Usage: php check_yt.php <video_id> <api_key>\n";
    exit(1);
}
$url = 'https://www.googleapis.com/youtube/v3/videos?part=status,contentDetails&id=' . urlencode($id) . '&key=' . urlencode($key);
$resp = @file_get_contents($url);
if ($resp === false) {
    echo "ERROR: failed to fetch API\n";
    exit(2);
}
echo $resp . "\n";
