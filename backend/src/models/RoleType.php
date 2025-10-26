<?php
class RoleType {
    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT * FROM role_types ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM role_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
