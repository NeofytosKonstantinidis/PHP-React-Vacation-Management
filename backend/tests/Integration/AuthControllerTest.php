<?php
// tests/Integration/AuthControllerTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/controllers/AuthController.php';
require_once __DIR__ . '/../../src/models/User.php';

class AuthControllerTest extends TestCase
{
    private $pdo;
    
    protected function setUp(): void
    {
        $this->pdo = getTestPDO();
        cleanTestDatabase();
        $this->seedTestData();
    }
    
    private function seedTestData()
    {
        // Insert role types
        $this->pdo->exec("
            INSERT INTO role_types (id, name) VALUES 
            (1, 'manager'), 
            (2, 'employee')
        ");
        
        // Insert schedule types
        $this->pdo->exec("
            INSERT INTO schedule_types (id, name, description, work_days) VALUES 
            (1, '5 Day Schedule', 'Monday to Friday', 'Mon,Tue,Wed,Thu,Fri'),
            (2, '6 Day Schedule', 'Monday to Saturday', 'Mon,Tue,Wed,Thu,Fri,Sat')
        ");
        
        // Insert test user
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $this->pdo->exec("
            INSERT INTO users (id, name, email, username, password_hash, role_id, schedule_id, vacation_days) 
            VALUES (1, 'Test User', 'test@example.com', 'testuser', '{$hashedPassword}', 2, 1, 20)
        ");
    }
    
    public function testLoginWithValidCredentials()
    {
        $data = [
            'username' => 'testuser',
            'password' => 'password123'
        ];
        
        $result = AuthController::login($this->pdo, $data);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals('Test User', $result['user']['name']);
    }
    
    public function testLoginWithInvalidPassword()
    {
        $data = [
            'username' => 'testuser',
            'password' => 'wrongpassword'
        ];
        
        $result = AuthController::login($this->pdo, $data);
        
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Invalid username or password', $result['error']);
    }
    
    public function testLoginWithNonExistentUsername()
    {
        $data = [
            'username' => 'nonexistent',
            'password' => 'password123'
        ];
        
        $result = AuthController::login($this->pdo, $data);
        
        $this->assertArrayHasKey('error', $result);
    }
    
    public function testLoginWithMissingFields()
    {
        $data = ['username' => 'testuser'];
        
        $result = AuthController::login($this->pdo, $data);
        
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Missing username or password', $result['error']);
    }
    
    protected function tearDown(): void
    {
        cleanTestDatabase();
    }
}