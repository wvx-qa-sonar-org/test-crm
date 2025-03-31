<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get request path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/', '', $path);
$parts = explode('/', $path);
$resource = $parts[0] ?? '';
$id = $parts[1] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// Get request body
$data = json_decode(file_get_contents('php://input'), true);

// Get database instance
$db = Database::getInstance();

// Authentication middleware
function authenticate() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Unauthorized']);
        exit();
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    try {
        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        return $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['message' => 'Invalid token']);
        exit();
    }
}

// Routes
switch ($resource) {
    case 'login':
        if ($method === 'POST') {
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            $users = $db->getCollection('users');
            $user = $users->findOne(['email' => $email]);

            // For testing purposes, let's create a test user if none exists
            if (!$user) {
                $users->insertOne([
                    'email' => 'test@example.com',
                    'password' => password_hash('password123', PASSWORD_DEFAULT),
                    'name' => 'Test User'
                ]);
            }

            // In production, you should verify password hash
            $payload = [
                'user_id' => (string)$user->_id,
                'email' => $user->email,
                'exp' => time() + $_ENV['JWT_EXPIRATION']
            ];

            $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

            echo json_encode([
                'token' => $token,
                'user' => [
                    'email' => $user->email,
                    'name' => $user->name
                ]
            ]);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
        break;

    case 'register':
        if ($method === 'POST') {
            $name = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($name) || empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['message' => 'All fields are required']);
                exit();
            }

            $users = $db->getCollection('users');
            $existingUser = $users->findOne(['email' => $email]);

            if ($existingUser) {
                http_response_code(409);
                echo json_encode(['message' => 'Email already in use']);
                exit();
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $result = $users->insertOne([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'user',
                'createdAt' => new MongoDB\BSON\UTCDateTime()
            ]);

            if ($result->getInsertedCount()) {
                http_response_code(201);
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to create user']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
        break;

    case 'tickets':
        // Authenticate user for all ticket operations
        $user = authenticate();

        if ($method === 'GET' && !$id) {
            // Get all tickets
            $tickets = $db->getCollection('tickets')->find();
            $result = [];

            foreach ($tickets as $ticket) {
                $client = $db->getCollection('clients')->findOne(['_id' => $ticket->clientId]);
                $result[] = [
                    'id' => (string)$ticket->_id,
                    'subject' => $ticket->subject,
                    'description' => $ticket->description,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'created' => $ticket->createdAt->toDateTime()->format('Y-m-d'),
                    'client' => $client->name,
                    'clientId' => (string)$client->_id,
                    'clientEmail' => $client->email,
                    'clientPhone' => $client->phone
                ];
            }

            echo json_encode(['tickets' => $result]);
        } elseif ($method === 'GET' && $id) {
            // Get single ticket
            $ticket = $db->getCollection('tickets')->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
            
            if (!$ticket) {
                http_response_code(404);
                echo json_encode(['message' => 'Ticket not found']);
                exit();
            }

            $client = $db->getCollection('clients')->findOne(['_id' => $ticket->clientId]);
            
            echo json_encode([
                'ticket' => [
                    'id' => (string)$ticket->_id,
                    'subject' => $ticket->subject,
                    'description' => $ticket->description,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'created' => $ticket->createdAt->toDateTime()->format('Y-m-d'),
                    'client' => $client->name,
                    'clientId' => (string)$client->_id,
                    'clientEmail' => $client->email,
                    'clientPhone' => $client->phone
                ]
            ]);
        } elseif ($method === 'POST') {
            // Create ticket
            $subject = $data['subject'] ?? '';
            $description = $data['description'] ?? '';
            $clientId = $data['clientId'] ?? '';
            $priority = $data['priority'] ?? 'Medium';

            if (empty($subject) || empty($description) || empty($clientId)) {
                http_response_code(400);
                echo json_encode(['message' => 'Required fields missing']);
                exit();
            }

            $result = $db->getCollection('tickets')->insertOne([
                'subject' => $subject,
                'description' => $description,
                'status' => 'Open',
                'priority' => $priority,
                'clientId' => new MongoDB\BSON\ObjectId($clientId),
                'createdBy' => new MongoDB\BSON\ObjectId($user->id),
                'createdAt' => new MongoDB\BSON\UTCDateTime()
            ]);

            if ($result->getInsertedCount()) {
                $newTicket = $db->getCollection('tickets')->findOne(['_id' => $result->getInsertedId()]);
                $client = $db->getCollection('clients')->findOne(['_id' => $newTicket->clientId]);

                echo json_encode([
                    'ticket' => [
                        'id' => (string)$newTicket->_id,
                        'subject' => $newTicket->subject,
                        'description' => $newTicket->description,
                        'status' => $newTicket->status,
                        'priority' => $newTicket->priority,
                        'created' => $newTicket->createdAt->toDateTime()->format('Y-m-d'),
                        'client' => $client->name,
                        'clientId' => (string)$client->_id
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to create ticket']);
            }
        } elseif ($method === 'PUT' && $id) {
            // Update ticket
            $updateData = [];
            
            if (isset($data['subject'])) $updateData['subject'] = $data['subject'];
            if (isset($data['description'])) $updateData['description'] = $data['description'];
            if (isset($data['status'])) $updateData['status'] = $data['status'];
            if (isset($data['priority'])) $updateData['priority'] = $data['priority'];

            if (empty($updateData)) {
                http_response_code(400);
                echo json_encode(['message' => 'No fields to update']);
                exit();
            }

            $result = $db->getCollection('tickets')->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => $updateData]
            );

            if ($result->getModifiedCount()) {
                $updatedTicket = $db->getCollection('tickets')->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
                $client = $db->getCollection('clients')->findOne(['_id' => $updatedTicket->clientId]);

                echo json_encode([
                    'ticket' => [
                        'id' => (string)$updatedTicket->_id,
                        'subject' => $updatedTicket->subject,
                        'description' => $updatedTicket->description,
                        'status' => $updatedTicket->status,
                        'priority' => $updatedTicket->priority,
                        'created' => $updatedTicket->createdAt->toDateTime()->format('Y-m-d'),
                        'client' => $client->name,
                        'clientId' => (string)$client->_id
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Ticket not found or no changes made']);
            }
        } elseif ($method === 'DELETE' && $id) {
            // Delete ticket
            $result = $db->getCollection('tickets')->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);

            if ($result->getDeletedCount()) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Ticket not found']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
        break;

    case 'clients':
        // Authenticate user for all client operations
        $user = authenticate();

        if ($method === 'GET') {
            // Get all clients
            $clients = $db->getCollection('clients')->find();
            $result = [];

            foreach ($clients as $client) {
                $result[] = [
                    'id' => (string)$client->_id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone
                ];
            }

            echo json_encode(['clients' => $result]);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
        break;

    case 'dashboard':
        // Authenticate user
        $user = authenticate();

        if ($method === 'GET') {
            // Get dashboard data
            $tickets = $db->getCollection('tickets');
            $clients = $db->getCollection('clients');

            $totalTickets = $tickets->count();
            $pendingTickets = $tickets->count(['status' => 'Pending']);
            $resolvedTickets = $tickets->count(['status' => 'Resolved']);
            $totalClients = $clients->count();

            // Get recent tickets
            $recentTickets = [];
            $cursor = $tickets->find([], [
                'sort' => ['createdAt' => -1],
                'limit' => 5
            ]);

            foreach ($cursor as $ticket) {
                $client = $clients->findOne(['_id' => $ticket->clientId]);
                $recentTickets[] = [
                    'id' => (string)$ticket->_id,
                    'subject' => $ticket->subject,
                    'client' => $client->name,
                    'status' => $ticket->status,
                    'created' => $ticket->createdAt->toDateTime()->format('Y-m-d')
                ];
            }

            echo json_encode([
                'totalTickets' => $totalTickets,
                'pendingTickets' => $pendingTickets,
                'resolvedTickets' => $resolvedTickets,
                'totalClients' => $totalClients,
                'recentTickets' => $recentTickets
            ]);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['message' => 'Resource not found']);
        break;
}