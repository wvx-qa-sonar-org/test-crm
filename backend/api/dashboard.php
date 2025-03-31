<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Database;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $db = Database::getInstance();
    
    // Get total clients
    $totalClients = $db->getCollection('clients')->countDocuments();
    
    // Get total tickets
    $totalTickets = $db->getCollection('tickets')->countDocuments();
    
    // Get tickets by status
    $ticketsByStatus = [
        'open' => $db->getCollection('tickets')->countDocuments(['status' => 'Open']),
        'pending' => $db->getCollection('tickets')->countDocuments(['status' => 'Pending']),
        'closed' => $db->getCollection('tickets')->countDocuments(['status' => 'Closed'])
    ];
    
    // Get recent activities (last 5 tickets)
    $recentTickets = $db->getCollection('tickets')
        ->find(
            [],
            [
                'sort' => ['created' => -1],
                'limit' => 5,
                'projection' => [
                    'subject' => 1,
                    'status' => 1,
                    'created' => 1,
                    'client' => 1
                ]
            ]
        )->toArray();
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'totalClients' => $totalClients,
            'totalTickets' => $totalTickets,
            'ticketsByStatus' => $ticketsByStatus,
            'recentActivities' => $recentTickets
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}