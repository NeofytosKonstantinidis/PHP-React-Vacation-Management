<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../config/config.php';

class AuthController {
    public static function login($pdo, $data) {
        if (!Validator::required($data, ['username', 'password'])) {
            http_response_code(400);
            return ["error" => "Missing username or password"];
        }

        $stmt = $pdo->prepare("
            SELECT u.*, r.name AS role_name
            FROM users u
            JOIN role_types r ON u.role_id = r.id
            WHERE u.username = ?
        ");
        $stmt->execute([$data['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            http_response_code(401);
            return ["error" => "Invalid username or password"];
        }

        // Load config for JWT settings
        $config = require __DIR__ . '/../config/config.php';
        
        $payload = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role_name'],
            'exp' => time() + $config['jwt']['expiry']
        ];
        $token = JWT::encode($payload, $config['jwt']['secret_key']);

        return [
            "message" => "Login successful",
            "token" => $token,
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email'],
                "role" => $user['role_name']
            ]
        ];
    }
}
