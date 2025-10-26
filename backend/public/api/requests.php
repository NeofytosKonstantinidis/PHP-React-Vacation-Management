<?php
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/utils/Cors.php';
require_once __DIR__ . '/../../src/utils/Auth.php';
require_once __DIR__ . '/../../src/utils/Response.php';
require_once __DIR__ . '/../../src/utils/VacationCalculator.php';
require_once __DIR__ . '/../../src/controllers/RequestController.php';

$config = require __DIR__ . '/../../src/config/config.php';
Cors::apply($config);

$pdo = getPDOConnection();
$method = $_SERVER['REQUEST_METHOD'];
$payload = Auth::requireAuth();

switch ($method) {
    case 'GET':
        // Handle calculate endpoint
        if (isset($_GET['action']) && $_GET['action'] === 'calculate') {
            $startDate = $_GET['start_date'] ?? null;
            $endDate = $_GET['end_date'] ?? null;
            
            if (!$startDate || !$endDate) {
                http_response_code(400);
                Response::error("start_date and end_date are required");
                exit;
            }
            
            $result = VacationCalculator::calculateVacationDays($pdo, $payload['id'], $startDate, $endDate);
            Response::json($result);
            exit;
        }
        
        // Handle remaining days endpoint
        if (isset($_GET['action']) && $_GET['action'] === 'remaining') {
            $result = VacationCalculator::getRemainingDays($pdo, $payload['id']);
            Response::json($result);
            exit;
        }
        
        // Employee ->  Can only see their own requests
        if ($payload['role'] === 'employee') {
            Response::json(RequestController::getByEmployee($pdo, $payload['id']));
        } else {
            // Manager -> Can see all requests
            Response::json(RequestController::getAll($pdo));
        }
        break;

    case 'POST':
        // Both employees and managers can create vacation requests
        $data = json_decode(file_get_contents("php://input"), true);
        $data['employee_id'] = $payload['id']; // Always the authenticated user
        Response::json(RequestController::create($pdo, $data, $payload['id']));
        break;

    case 'PUT':
        // Μόνο manager μπορεί να αλλάξει status
        Auth::requireRole($payload, ['manager']);
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);
        Response::json(RequestController::update($pdo, $id, $data, $payload['id']));
        break;

    case 'DELETE':
        // Employee μπορεί να διαγράψει ΜΟΝΟ pending requests
        Auth::requireRole($payload, ['employee']);
        $id = $_GET['id'] ?? null;
        Response::json(RequestController::delete($pdo, $id, $payload['id']));
        break;

    default:
        http_response_code(405);
        Response::error("Method not allowed");
}
