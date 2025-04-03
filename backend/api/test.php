<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Database;

header('Content-Type: application/json');

try {
    // Test database connection
    $db = Database::getInstance();
    $result = $db->getCollection('clients')->findOne();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'MongoDB connection successful',
        'data' => $result
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'line' => $e->getLine()
    ]);
}