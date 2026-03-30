<?php

class Database {
    private $conn;
    private $host;
    private $user;
    private $password;
    private $baseName;

    function __construct() {
        $this->host = 'localhost';
        $this->user = 'root';
        $this->password = '';
        $this->baseName= 'artportal';
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
            catch (Exception $e) {
                die('Connection failed : ' . $e->getMessage());
            }
        }
        return $this->conn;
        return $this->baseName;
    }

    function disconnect() {
        if ($this->conn) {
            $this->conn = null;
        }
    }

    function getOne($query, $params = []) {
        $this->connect();
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $stmt->setFetchMode (PDO::FETCH_ASSOC);
        $response = $stmt->fetch();
        return $response;
    }

    function getAll($query, $params = []) {
        $this->connect();
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $response = $stmt->fetchAll();
        return $response;
    }

    function executeRun($query, $params = []) {
        $this->connect();
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }
}