<?php
class AuditLog {
    public static function getAll($pdo) {
        $stmt = $pdo->query("
            SELECT a.*, u.name AS user_name
            FROM audit_log a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($pdo, $userId, $action, $details = null) {
        $stmt = $pdo->prepare("
            INSERT INTO audit_log (user_id, action, details)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $action, $details]);
    }
}
