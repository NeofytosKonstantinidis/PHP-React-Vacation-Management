<?php
class VacationRequest {
    public static function getAll($pdo) {
        $stmt = $pdo->query("
            SELECT vr.*, 
                   u.name AS employee_name, 
                   u.vacation_days,
                   s.name AS status_name
            FROM vacation_requests vr
            JOIN users u ON vr.employee_id = u.id
            JOIN vacation_status_types s ON vr.status_id = s.id
            ORDER BY vr.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByEmployee($pdo, $employeeId) {
        $stmt = $pdo->prepare("
            SELECT vr.*, s.name AS status_name
            FROM vacation_requests vr
            JOIN vacation_status_types s ON vr.status_id = s.id
            WHERE vr.employee_id = ?
            ORDER BY vr.created_at DESC
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($pdo, $data) {
        $stmt = $pdo->prepare("
            INSERT INTO vacation_requests (employee_id, start_date, end_date, reason, status_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['employee_id'],
            $data['start_date'],
            $data['end_date'],
            $data['reason'],
            $data['status_id'] ?? 1
        ]);
        return $pdo->lastInsertId();
    }

    public static function update($pdo, $id, $data) {
        $fields = [];
        $values = [];

        if (isset($data['status_id'])) { $fields[] = "status_id=?"; $values[] = $data['status_id']; }
        if (isset($data['start_date'])) { $fields[] = "start_date=?"; $values[] = $data['start_date']; }
        if (isset($data['end_date'])) { $fields[] = "end_date=?"; $values[] = $data['end_date']; }
        if (isset($data['reason'])) { $fields[] = "reason=?"; $values[] = $data['reason']; }

        if (!empty($fields)) {
            $sql = "UPDATE vacation_requests SET " . implode(", ", $fields) . " WHERE id=?";
            $values[] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
        }
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM vacation_requests WHERE id=?");
        $stmt->execute([$id]);
    }
}
