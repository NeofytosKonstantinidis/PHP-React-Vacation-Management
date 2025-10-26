<?php
class User {
    public static function getAll($pdo) {
        $stmt = $pdo->query("
            SELECT u.*, r.name AS role_name, st.name AS schedule_name
            FROM users u
            JOIN role_types r ON u.role_id = r.id
            JOIN schedule_types st ON u.schedule_id = st.id
            ORDER BY u.id ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($pdo, $id) {
        $stmt = $pdo->prepare("
            SELECT u.*, r.name AS role_name, st.name AS schedule_name
            FROM users u
            JOIN role_types r ON u.role_id = r.id
            JOIN schedule_types st ON u.schedule_id = st.id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($pdo, $data) {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                throw new Exception("Email already exists");
            }

            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetch()) {
                throw new Exception("Username already exists");
            }

            // Password length validation
            if (!isset($data['password']) || strlen($data['password']) < 6) {
                throw new Exception("Password must be at least 6 characters long");
            }

            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, username, password_hash, role_id, schedule_id, vacation_days)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['username'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['role_id'],
                $data['schedule_id'],
                $data['vacation_days'] ?? 20
            ]);
            return $pdo->lastInsertId();
    }

    public static function update($pdo, $id, $data) {
            // Check if email already exists for another user
            if (isset($data['email'])) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$data['email'], $id]);
                if ($stmt->fetch()) {
                    throw new Exception("Email already exists");
                }
            }

            // Check if username already exists for another user
            if (isset($data['username'])) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
                $stmt->execute([$data['username'], $id]);
                if ($stmt->fetch()) {
                    throw new Exception("Username already exists");
                }
            }

            $fields = [];
            $values = [];

            if (isset($data['name'])) { $fields[] = "name=?"; $values[] = $data['name']; }
            if (isset($data['email'])) { $fields[] = "email=?"; $values[] = $data['email']; }
            if (isset($data['username'])) { $fields[] = "username=?"; $values[] = $data['username']; }
            if (isset($data['role_id'])) { $fields[] = "role_id=?"; $values[] = $data['role_id']; }
            if (isset($data['schedule_id'])) { $fields[] = "schedule_id=?"; $values[] = $data['schedule_id']; }
            if (isset($data['vacation_days'])) { $fields[] = "vacation_days=?"; $values[] = $data['vacation_days']; }
            if (isset($data['password'])) {
                // Password length validation
                if (strlen($data['password']) < 6) {
                    throw new Exception("Password must be at least 6 characters long");
                }
                $fields[] = "password_hash=?";
                $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            if (!empty($fields)) {
                $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id=?";
                $values[] = $id;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
            }
    }

    public static function delete($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$id]);
    }
}
