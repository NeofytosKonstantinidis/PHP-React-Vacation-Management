<?php
require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/../config/config.php';

class Auth
{
    private static $secret = null;
    
    private static function getSecret()
    {
        if (self::$secret === null) {
            $config = require __DIR__ . '/../config/config.php';
            self::$secret = $config['jwt']['secret_key'];
        }
        return self::$secret;
    } 

    /**
     * Authenticates the JWT token from the Authorization header.
     * If valid, returns the payload (user info).
     * If not, exits with 401.
     */
    public static function requireAuth()
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["error" => "Missing Authorization header"]);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $payload = JWT::decode($token, self::getSecret());

        if (!$payload) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid or expired token"]);
            exit;
        }

        return $payload; // contains user id, email, role, exp date
    }

    /**
     * Checks if the user's role is one of the allowed ones.
     * If not, returns 403.
     */
    public static function requireRole($payload, $allowedRoles)
    {
        if (!in_array($payload['role'], $allowedRoles)) {
            http_response_code(403);
            echo json_encode(["error" => "Forbidden: insufficient permissions"]);
            exit;
        }
    }
}
