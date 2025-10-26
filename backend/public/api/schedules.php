<?php
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/utils/Cors.php';
require_once __DIR__ . '/../../src/utils/Auth.php';
require_once __DIR__ . '/../../src/utils/Response.php';
require_once __DIR__ . '/../../src/controllers/ScheduleController.php';

$config = require __DIR__ . '/../../src/config/config.php';
Cors::apply($config);

$pdo = getPDOConnection();
$method = $_SERVER['REQUEST_METHOD'];
$payload = Auth::requireAuth();

switch ($method) {
    case 'GET':
        if ($payload['role'] === 'manager') {
            Response::json(ScheduleController::getAll($pdo));
        } else {
            // Employees βλέπουν μόνο το δικό τους
            Response::json(ScheduleController::getByEmployee($pdo, $payload['id']));
        }
        break;

    case 'POST':
        Auth::requireRole($payload, ['manager']);
        $data = json_decode(file_get_contents("php://input"), true);
        Response::json(ScheduleController::create($pdo, $data, $payload['id']));
        break;

    case 'PUT':
        Auth::requireRole($payload, ['manager']);
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);
        Response::json(ScheduleController::update($pdo, $id, $data, $payload['id']));
        break;

    case 'DELETE':
        Auth::requireRole($payload, ['manager']);
        $id = $_GET['id'] ?? null;
        Response::json(ScheduleController::delete($pdo, $id, $payload['id']));
        break;

    default:
        http_response_code(405);
        Response::error("Method not allowed");
}
