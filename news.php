<?php
require_once __DIR__ . '/config.php'; // contains NEWS_API_KEY
header('Content-Type: application/json');

$action = $_GET['action'] ?? 'headlines';
$q = trim($_GET['q'] ?? '');

function callNewsApi($endpoint, $params = []) {
    $baseUrl = 'https://newsapi.org/v2/';
    $query   = http_build_query($params);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $baseUrl . $endpoint . '?' . $query,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'X-Api-Key: ' . NEWS_API_KEY,
        ],
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        echo json_encode(['status' => 'error', 'message' => 'cURL error: ' . $error]);
        exit;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($httpCode !== 200 || !is_array($data)) {
        echo json_encode(['status' => 'error', 'message' => $data['message'] ?? 'Unknown API error']);
        exit;
    }

    echo json_encode($data);
    exit;
}

if ($action === 'search') {
    if ($q === '') {
        echo json_encode(['status' => 'error', 'message' => 'Missing search query']);
        exit;
    }
    callNewsApi('everything', [
        'q' => $q,
        'language' => 'en',
        'sortBy' => 'publishedAt',
        'pageSize' => 20,
    ]);
} else {
    callNewsApi('top-headlines', [
        'country' => 'us',
        'pageSize' => 20,
    ]);
}
?>
