<?php
class Validator {

    // Check if required fields exist and are non-empty
    public static function required($data, $fields) {
        foreach ($fields as $f) {
            if (!isset($data[$f]) || trim($data[$f]) === '') return false;
        }
        return true;
    }

    // Validate email format
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Check string length range
    public static function length($value, $min = 0, $max = null) {
        $len = strlen($value);
        if ($max !== null && $len > $max) return false;
        if ($len < $min) return false;
        return true;
    }

    // Generic sanitization (optional use)
    public static function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}
