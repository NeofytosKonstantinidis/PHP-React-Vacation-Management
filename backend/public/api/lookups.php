<?php
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/utils/Cors.php';
require_once __DIR__ . '/../../src/utils/Response.php';
require_once __DIR__ . '/../../src/controllers/LookupController.php';

$config = require __DIR__ . '/../../src/config/config.php';
Cors::apply($config);

$pdo = getPDOConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    http_response_code(405);
    Response::error("Method not allowed");
    exit;
}

$type = $_GET['type'] ?? null;
switch ($type) {
    case 'roles':
        Response::json(LookupController::getRoles($pdo));
        break;
    case 'statuses':
        Response::json(LookupController::getStatuses($pdo));
        break;
    case 'schedule_types':
        Response::json(LookupController::getScheduleTypes($pdo));
        break;
    default:
        http_response_code(400);
        Response::error("Invalid lookup type");
}
