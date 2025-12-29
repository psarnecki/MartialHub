<?php

require_once "config.php";

class Database {
    private static ?Database $instance = null;
    private $username;
    private $password;
    private $host;
    private $database;
    private ?PDO $conn = null;

    public function __construct()
    {
        $this->username = USERNAME;
        $this->password = PASSWORD;
        $this->host = HOST;
        $this->database = DATABASE;
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function connect()
    {
        if ($this->conn === null) {
            try {
                $this->conn = new PDO(
                    "pgsql:host=$this->host;port=5432;dbname=$this->database",
                    $this->username,
                    $this->password,
                    ["sslmode"  => "prefer"]
                );

                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return $this->conn;
    }

    public function disconnect() {
        $this->conn = null;
    }
}