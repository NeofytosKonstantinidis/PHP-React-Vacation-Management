<?php
require_once __DIR__ . '/../models/VacationRequest.php';
require_once __DIR__ . '/../models/AuditLog.php';
require_once __DIR__ . '/../utils/VacationCalculator.php';

class RequestController {
    public static function getAll($pdo) {
        $requests = VacationRequest::getAll($pdo);
        
        // Add calculated work days and remaining balance for each request
        foreach ($requests as &$request) {
            $calculation = VacationCalculator::calculateVacationDays(
                $pdo, 
                $request['employee_id'], 
                $request['start_date'], 
                $request['end_date']
            );
            $request['calculated_days'] = $calculation['days'];
            
            // Get remaining days for this employee
            $remaining = VacationCalculator::getRemainingDays($pdo, $request['employee_id']);
            $request['remaining_days'] = $remaining['remaining'];
        }
        
        return $requests;
    }

    public static function getByEmployee($pdo, $employeeId) {
        return VacationRequest::getByEmployee($pdo, $employeeId);
    }

    public static function create($pdo, $data, $actorId = null) {
        $id = VacationRequest::create($pdo, $data);
        AuditLog::create($pdo, $actorId, 'request_created', "Request ID $id created");
        return ["message" => "Request created successfully", "id" => $id];
    }

    public static function update($pdo, $id, $data, $actorId = null) {
        VacationRequest::update($pdo, $id, $data);
        AuditLog::create($pdo, $actorId, 'request_updated', "Request ID $id updated");
        return ["message" => "Request updated successfully"];
    }

    public static function delete($pdo, $id, $actorId = null) {
        VacationRequest::delete($pdo, $id);
        AuditLog::create($pdo, $actorId, 'request_deleted', "Request ID $id deleted");
        return ["message" => "Request deleted successfully"];
    }
}
