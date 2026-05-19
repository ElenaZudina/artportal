<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

/**
 * Choose env file
 */
$envFile = '.env';

if (
    isset($_SERVER['APP_ENV']) &&
    $_SERVER['APP_ENV'] === 'test'
) {
    $envFile = '.env.test';
}

/**
 * Load environment variables from .env file
 * Stores database credentials: DB_HOST, DB_USER, DB_PASSWORD, DB_NAME
 */
$dotenv = Dotenv::createImmutable(
    __DIR__ . '/../',
    $envFile
);
$dotenv->load();

/**
 * Database Connection Class - handles PDO database operations
 * Manages MySQL connection with lazy loading (connects only when needed)
 * Uses environment variables for credentials
 */
class Database {
    private $conn;
    private $host;
    private $user;
    private $password;
    private $baseName;

    /**
     * Constructor - initialize database credentials from environment variables
     * Does not automatically connect (lazy loading pattern)
     */
    function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->user = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->baseName = trim($_ENV['DB_NAME'] ?? '');
        if ($this->baseName === '') {
            throw new Exception('Database name is not configured');
        }
        //$this->connect(); убрала автоматическое подключение 
        //при создании объекта, чтобы не было лишних подключений при каждом новом объекте
    }
    /**
     * Destructor - automatically closes database connection when object is destroyed
     */
    function __destruct() {
        $this->disconnect();
    }

    /**
     * Establish database connection using PDO
     * Lazy loading - creates connection only on first call
     * Throws exception if connection fails
     * @return PDO Database connection object
     */
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

    /**
     * Close database connection
     * Sets connection to null, releasing resources
     */
    function disconnect() {
        if ($this->conn) {
            $this->conn = null;
        }
    }

    /**
     * Execute SELECT query and return first row
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return array|false First row as associative array or false if not found
     * @throws Exception on database error
     */
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

    /**
     * Execute SELECT query and return all rows
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return array Result set as array of associative arrays
     * @throws Exception on database error
     */
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

    /**
     * Execute INSERT, UPDATE, or DELETE query
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return PDOStatement Executed statement
     * @throws Exception on database error
     */
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

    /**
     * Get the ID of the last inserted row
     * @return string Last insert ID
     */
    function getLastInsertId() {
        return $this->conn->lastInsertId();
    }
}
