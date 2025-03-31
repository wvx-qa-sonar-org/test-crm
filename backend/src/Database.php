<?php

namespace App;

use MongoDB\Client;
use MongoDB\Collection;
use Dotenv\Dotenv;

class Database
{
    private static $instance = null;
    private $client;
    private $db;

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        $this->client = new Client($_ENV['MONGODB_URI']);
        $this->db = $this->client->crm;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getCollection($name): Collection
    {
        return $this->db->$name;
    }
}