<?php
class VacationStatusType {
    public static function getAll($pdo) {
        $stmt = $pdo->query("SELECT * FROM vacation_status_types ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare("SELECT * FROM vacation_status_types WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
