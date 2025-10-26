<?php
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/utils/Cors.php';
require_once __DIR__ . '/../../src/utils/Auth.php';
require_once __DIR__ . '/../../src/utils/Response.php';
require_once __DIR__ . '/../../src/controllers/UserController.php';

$config = require __DIR__ . '/../../src/config/config.php';
Cors::apply($config);

$pdo = getPDOConnection();
$method = $_SERVER['REQUEST_METHOD'];

// JWT validation
$payload = Auth::requireAuth();

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Employees can only view themselves, managers can view anyone
            $requestedId = $_GET['id'];
            if ($payload['role'] !== 'manager' && $payload['id'] != $requestedId) {
                http_response_code(403);
                Response::error("Forbidden: You can only view your own profile");
                exit;
            }
            Response::json(UserController::getById($pdo, $requestedId));
        } else {
            // Only managers can view all users
            Auth::requireRole($payload, ['manager']);
            Response::json(UserController::getAll($pdo));
        }
        break;

    case 'POST':
        // Μόνο manager μπορεί να δημιουργήσει χρήστες
        Auth::requireRole($payload, ['manager']);
        $data = json_decode(file_get_contents("php://input"), true);
        try {
            Response::json(UserController::create($pdo, $data, $payload['id']));
        } catch (Exception $e) {
            http_response_code(400);
            Response::error($e->getMessage());
        }
        break;

    case 'PUT':
        // Μόνο manager
        Auth::requireRole($payload, ['manager']);
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);
        try {
            Response::json(UserController::update($pdo, $id, $data, $payload['id']));
        } catch (Exception $e) {
            http_response_code(400);
            Response::error($e->getMessage());
        }
        break;

    case 'DELETE':
        // Μόνο manager
        Auth::requireRole($payload, ['manager']);
        $id = $_GET['id'] ?? null;
        Response::json(UserController::delete($pdo, $id, $payload['id']));
        break;

    default:
        http_response_code(405);
        Response::error("Method not allowed");
}
