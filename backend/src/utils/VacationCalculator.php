<?php
class VacationCalculator {
    /**
     * Calculate vacation days based on work schedule
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User ID
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @return array ['days' => int, 'work_days' => array]
     */
    public static function calculateVacationDays($pdo, $userId, $startDate, $endDate) {
        // Get user's work schedule from schedule_types table
        $stmt = $pdo->prepare("
            SELECT st.work_days
            FROM users u
            JOIN schedule_types st ON u.schedule_id = st.id
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$schedule || empty($schedule['work_days'])) {
            // Default to 5-day week if no schedule found
            $workDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        } else {
            // Parse work_days from schedule_types table
            $workDays = explode(',', $schedule['work_days']);
        }
        
        // Calculate days
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day'); // Include end date
        
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);
        
        $vacationDays = 0;
        $daysList = [];
        
        foreach ($period as $date) {
            $dayName = $date->format('D'); // Mon, Tue, etc.
            
            // Check if this day is a work day
            if (in_array($dayName, $workDays)) {
                $vacationDays++;
                $daysList[] = $date->format('Y-m-d');
            }
        }
        
        return [
            'days' => $vacationDays,
            'work_days' => $daysList,
            'schedule' => $workDays
        ];
    }
    
    /**
     * Get remaining vacation days for a user
     */
    public static function getRemainingDays($pdo, $userId) {
        // Get allocated days
        $stmt = $pdo->prepare("SELECT vacation_days FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $allocatedDays = $user['vacation_days'] ?? 20;
        
        // Get used days (approved requests only)
        $stmt = $pdo->prepare("
            SELECT SUM(DATEDIFF(end_date, start_date) + 1) as used_days
            FROM vacation_requests
            WHERE employee_id = ? AND status_id = 2
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $usedDays = $result['used_days'] ?? 0;
        
        return [
            'allocated' => $allocatedDays,
            'used' => $usedDays,
            'remaining' => $allocatedDays - $usedDays
        ];
    }
}
