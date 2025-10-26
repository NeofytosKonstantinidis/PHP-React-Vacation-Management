<?php
require_once __DIR__ . '/../models/WorkSchedule.php';
require_once __DIR__ . '/../models/AuditLog.php';

class ScheduleController {
    public static function getAll($pdo) {
        return WorkSchedule::getAll($pdo);
    }

    public static function getByEmployee($pdo, $employeeId) {
        return WorkSchedule::getByEmployee($pdo, $employeeId);
    }

    public static function create($pdo, $data, $actorId = null) {
        $id = WorkSchedule::create($pdo, $data);
        AuditLog::create($pdo, $actorId, 'schedule_created', "Schedule ID $id created");
        return ["message" => "Schedule created successfully", "id" => $id];
    }

    public static function update($pdo, $id, $data, $actorId = null) {
        WorkSchedule::update($pdo, $id, $data);
        AuditLog::create($pdo, $actorId, 'schedule_updated', "Schedule ID $id updated");
        return ["message" => "Schedule updated successfully"];
    }

    public static function delete($pdo, $id, $actorId = null) {
        WorkSchedule::delete($pdo, $id);
        AuditLog::create($pdo, $actorId, 'schedule_deleted', "Schedule ID $id deleted");
        return ["message" => "Schedule deleted successfully"];
    }
}
