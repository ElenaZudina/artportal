<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class Database {
    private $conn;
    private $host;
    private $user;
    private $password;
    private $baseName;

    function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->user = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->baseName = $_ENV['DB_NAME'] ?? 'art_portal';
        //$this->connect(); убрала автоматическое подключение 
        //при создании объекта, чтобы не было лишних подключений при каждом новом объекте
    }
    function __destruct() {
        $this->disconnect();
    }

    function connect() {
        if (!$this->conn) {
            try {
                $this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->baseName.';charset=utf8mb4',
                $this->user,
                $this->password,
                //array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());
                throw new Exception('Database connection error');
            }
        }
        return $this->conn;
    }

    function disconnect() {
        if ($this->conn) {
            $this->conn = null;
        }
    }

    function getOne($query, $params = []) {
        try {
        $this->connect();
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $response = $stmt->fetch();
        return $response;
        } catch (PDOException $e) {
            error_log('Database query error: ' . $e->getMessage());
            throw new Exception('Database operation failed');
        }
    }

    function getAll($query, $params = []) {
        try {
            $this->connect();
            $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $response = $stmt->fetchAll();
        return $response;
        } catch (PDOException $e) {
            error_log('Database query error: ' . $e->getMessage());
            throw new Exception('Database operation failed');
        }
    }

    function executeRun($query, $params = []) {
        try {
            $this->connect();
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
    } catch (PDOException $e) {
            error_log('Database query error: ' . $e->getMessage());
            throw new Exception('Database operation failed');
        }
    }

    function getLastInsertId() {
        return $this->conn->lastInsertId();
    }
}