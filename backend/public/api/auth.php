<?php
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/utils/Response.php';
require_once __DIR__ . '/../../src/utils/Cors.php';
require_once __DIR__ . '/../../src/controllers/AuthController.php';

$config = require __DIR__ . '/../../src/config/config.php';
Cors::apply($config);

$pdo = getPDOConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        Response::json(AuthController::login($pdo, $data));
        break;

    default:
        http_response_code(405);
        Response::error("Method not allowed");
}
