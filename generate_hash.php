<?php
if (isset($_GET['password'])) {
    $password = $_GET['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "Password: ". $password . "<br>";
    echo "Hash: " . $hash;
} else {
    echo "Please provide a password parameter in the URL.";
}
?>