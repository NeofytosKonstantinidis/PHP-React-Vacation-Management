<?php
class WorkSchedule {
    public static function getAll($pdo) {
        $stmt = $pdo->query("
            SELECT ws.*, u.name AS employee_name, st.name AS schedule_name
            FROM work_schedule ws
            JOIN users u ON ws.employee_id = u.id
            JOIN schedule_types st ON ws.schedule_type_id = st.id
            ORDER BY ws.id ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByEmployee($pdo, $employeeId) {
        $stmt = $pdo->prepare("
            SELECT ws.*, st.name AS schedule_name
            FROM work_schedule ws
            JOIN schedule_types st ON ws.schedule_type_id = st.id
            WHERE ws.employee_id = ?
        ");
        $stmt->execute([$employeeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($pdo, $data) {
        $stmt = $pdo->prepare("
            INSERT INTO work_schedule (employee_id, schedule_type_id, work_days)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $data['employee_id'],
            $data['schedule_type_id'],
            implode(',', $data['work_days'])
        ]);
        return $pdo->lastInsertId();
    }

    public static function update($pdo, $id, $data) {
        $stmt = $pdo->prepare("
            UPDATE work_schedule
            SET schedule_type_id = ?, work_days = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['schedule_type_id'],
            implode(',', $data['work_days']),
            $id
        ]);
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM work_schedule WHERE id=?");
        $stmt->execute([$id]);
    }
}
