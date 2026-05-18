<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "art_portal";

try {
    // Manual connection check for the local MySQL database.
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception so connection/query errors are visible.
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$conn = null;
?>
