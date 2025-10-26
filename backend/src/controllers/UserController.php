<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AuditLog.php';

class UserController {
    public static function getAll($pdo) {
        return User::getAll($pdo);
    }

    public static function getById($pdo, $id) {
        return User::getById($pdo, $id);
    }

    public static function create($pdo, $data, $actorId = null) {
        $id = User::create($pdo, $data);
        AuditLog::create($pdo, $actorId, 'user_created', "User ID $id created");
        return User::getById($pdo, $id);
    }

    public static function update($pdo, $id, $data, $actorId = null) {
    User::update($pdo, $id, $data);
    AuditLog::create($pdo, $actorId, 'user_updated', "User ID $id updated");
    return ["message" => "User updated successfully"];
    }


    public static function delete($pdo, $id, $actorId = null) {
        User::delete($pdo, $id);
        AuditLog::create($pdo, $actorId, 'user_deleted', "User ID $id deleted");
        return ["message" => "User deleted successfully"];
    }
}
