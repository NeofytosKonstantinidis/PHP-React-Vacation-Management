<?php
class Cors
{
    public static function apply($config)
    {
        $cors = $config['cors'];

        // Επιτρεπόμενες πηγές (origins)
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        if (in_array('*', $cors['allowed_origins']) || in_array($origin, $cors['allowed_origins'])) {
            header("Access-Control-Allow-Origin: $origin");
        }

        header("Access-Control-Allow-Methods: " . implode(', ', $cors['allowed_methods']));
        header("Access-Control-Allow-Headers: " . implode(', ', $cors['allowed_headers']));
        header("Access-Control-Allow-Credentials: true");

        // Αν είναι preflight OPTIONS request — απαντάμε και σταματάμε εδώ
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
