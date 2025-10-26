<?php
// tests/Unit/ValidatorTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/utils/Validator.php';

class ValidatorTest extends TestCase
{
    public function testRequiredFieldsValidation()
    {
        $data = ['email' => 'test@example.com', 'password' => 'secret123'];
        
        $this->assertTrue(Validator::required($data, ['email', 'password']));
        $this->assertFalse(Validator::required($data, ['email', 'password', 'name']));
    }
    
    public function testRequiredWithEmptyString()
    {
        $data = ['email' => '', 'password' => 'secret123'];
        
        $this->assertFalse(Validator::required($data, ['email', 'password']));
    }
    
    public function testEmailValidation()
    {
        $this->assertTrue(Validator::email('user@example.com'));
        $this->assertTrue(Validator::email('test.user+tag@domain.co.uk'));
        $this->assertFalse(Validator::email('invalid-email'));
        $this->assertFalse(Validator::email('user@'));
        $this->assertFalse(Validator::email('@domain.com'));
    }
    
    public function testLengthValidation()
    {
        $this->assertTrue(Validator::length('hello', 3, 10));
        $this->assertTrue(Validator::length('hello', 5, 5));
        $this->assertFalse(Validator::length('hi', 3, 10));
        $this->assertFalse(Validator::length('hello world!', 3, 10));
    }
    
    public function testLengthWithoutMax()
    {
        $this->assertTrue(Validator::length('hello', 3));
        $this->assertTrue(Validator::length('hello world and more', 5));
        $this->assertFalse(Validator::length('hi', 3));
    }
    
    public function testSanitization()
    {
        $dirty = '<script>alert("xss")</script>';
        $clean = Validator::sanitize($dirty);
        
        $this->assertNotEquals($dirty, $clean);
        $this->assertStringNotContainsString('<script>', $clean);
    }
    
    public function testSanitizeTrimming()
    {
        $input = '  hello world  ';
        $output = Validator::sanitize($input);
        
        $this->assertEquals('hello world', $output);
    }
}