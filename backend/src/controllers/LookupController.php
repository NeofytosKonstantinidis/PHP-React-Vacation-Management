<?php
require_once __DIR__ . '/../models/RoleType.php';
require_once __DIR__ . '/../models/VacationStatusType.php';
require_once __DIR__ . '/../models/ScheduleType.php';

class LookupController {
    public static function getRoles($pdo) {
       return RoleType::getAll($pdo);
    }

    public static function getStatuses($pdo) {
        return VacationStatusType::getAll($pdo);
    }

    public static function getScheduleTypes($pdo) {
        return ScheduleType::getAll($pdo);
    }
}
