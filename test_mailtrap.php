<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/services/EmailService.php';

use PHPMailer\PHPMailer\PHPMailer;

echo "<h1>Mailtrap Connection Test</h1>";

// Загрузить .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "<p><strong>Mailtrap Configuration:</strong></p>";
echo "<ul>";
echo "<li>Host: " . ($_ENV['MAILTRAP_HOST'] ?? 'NOT SET') . "</li>";
echo "<li>Port: " . ($_ENV['MAILTRAP_PORT'] ?? 'NOT SET') . "</li>";
echo "<li>Username: " . ($_ENV['MAILTRAP_USERNAME'] ?? 'NOT SET') . "</li>";
echo "<li>Password: " . (isset($_ENV['MAILTRAP_PASSWORD']) && !empty($_ENV['MAILTRAP_PASSWORD']) ? '***SET***' : 'NOT SET') . "</li>";
echo "</ul>";

// Создать тестовое письмо
$mail = new PHPMailer(true);

try {
    echo "<p><strong>Connecting to Mailtrap...</strong></p>";
    
    $mail->isSMTP();
    $mail->Host = $_ENV['MAILTRAP_HOST'] ?? 'smtp.mailtrap.io';
    $mail->Port = $_ENV['MAILTRAP_PORT'] ?? 465;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Username = $_ENV['MAILTRAP_USERNAME'] ?? '';
    $mail->Password = $_ENV['MAILTRAP_PASSWORD'] ?? '';
    
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    // Проверить соединение
    if($mail->smtpConnect()) {
        echo "<div style='color: green; font-weight: bold;'>✓ Successfully connected to Mailtrap!</div>";
        $mail->smtpClose();
    } else {
        echo "<div style='color: red; font-weight: bold;'>✗ Failed to connect to Mailtrap</div>";
        echo "<p>Error: " . $mail->ErrorInfo . "</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; font-weight: bold;'>✗ Exception:</div>";
    echo "<p>" . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Back to site</a></p>";
?>
