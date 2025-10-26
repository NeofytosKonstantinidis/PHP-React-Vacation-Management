<?php
// public/index.php
header('Content-Type: application/json');

require_once __DIR__ . '/../src/config/database.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Rerouting from /api/{resource} to appropriate handler
$path = str_replace('/api', '', $requestUri);
$segments = array_values(array_filter(explode('/', $path)));

if (count($segments) === 0) {
    echo json_encode(["message" => "API Root"]);
    exit;
}

// Extract resource and optional ID
$resource = $segments[0];
$id = $segments[1] ?? null;

switch ($resource) {
    case 'auth':
        require_once __DIR__ . '/api/auth.php';
        break;

    case 'users':
        require_once __DIR__ . '/api/users.php';
        break;

    case 'requests':
        require_once __DIR__ . '/api/requests.php';
        break;

    case 'schedules':
        require_once __DIR__ . '/api/schedules.php';
        break;

    case 'lookups':
        require_once __DIR__ . '/api/lookups.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Unknown endpoint"]);
}
