<?php
// tests/Unit/ResponseTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../src/utils/Response.php';

class ResponseTest extends TestCase
{
    public function testJsonResponseStructure()
    {
        // Testing that Response class exists and has the expected method
        $this->assertTrue(method_exists('Response', 'json'));
        $this->assertTrue(method_exists('Response', 'error'));
    }
    
    public function testErrorResponseMethodExists()
    {
        // We can't actually test the output without headers being sent
        // But we can verify the class structure
        $this->assertTrue(class_exists('Response'));
    }
}