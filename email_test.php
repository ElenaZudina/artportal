<?php
session_start();

// Загрузить конфигурацию
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mailtrap Email Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">
    <div class="container">
        <h1 class="mb-4">📧 Mailtrap Connection Test</h1>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Current Configuration</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>Host:</strong></td>
                        <td><code><?php echo $_ENV['MAILTRAP_HOST'] ?? 'NOT SET'; ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Port:</strong></td>
                        <td><code><?php echo $_ENV['MAILTRAP_PORT'] ?? 'NOT SET'; ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td><code><?php echo $_ENV['MAILTRAP_USERNAME'] ?? 'NOT SET'; ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>Password:</strong></td>
                        <td><code><?php echo !empty($_ENV['MAILTRAP_PASSWORD']) ? '***SET***' : 'NOT SET'; ?></code></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Test Results</h5>
            </div>
            <div class="card-body">
                <?php
                    $mail = new PHPMailer(true);
                    
                    try {
                        echo "<p><strong>Attempting to connect...</strong></p>";
                        
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
                        
                        // Попытка подключиться
                        if($mail->smtpConnect()) {
                            echo "<div class='alert alert-success'>✓ <strong>Successfully connected to Mailtrap!</strong></div>";
                            $mail->smtpClose();
                            
                            // Теперь попробуем отправить письмо
                            echo "<p><strong>Sending test email...</strong></p>";
                            
                            $mail2 = new PHPMailer(true);
                            $mail2->isSMTP();
                            $mail2->Host = $_ENV['MAILTRAP_HOST'];
                            $mail2->Port = $_ENV['MAILTRAP_PORT'];
                            $mail2->SMTPAuth = true;
                            $mail2->SMTPSecure = 'ssl';
                            $mail2->Username = $_ENV['MAILTRAP_USERNAME'];
                            $mail2->Password = $_ENV['MAILTRAP_PASSWORD'];
                            $mail2->SMTPOptions = $mail->SMTPOptions;
                            
                            $mail2->setFrom('noreply@artportal.local', 'ArtPortal Test');
                            $mail2->addAddress('test@example.com', 'Test Recipient');
                            $mail2->Subject = 'Test Email from ArtPortal';
                            $mail2->isHTML(true);
                            $mail2->Body = '<h1>Test Email</h1><p>This is a test email from ArtPortal sent via Mailtrap.</p>';
                            
                            if ($mail2->send()) {
                                echo "<div class='alert alert-success'>✓ <strong>Test email sent successfully!</strong></div>";
                                echo "<p class='text-muted'>Check your Mailtrap Inbox to see the email.</p>";
                            } else {
                                echo "<div class='alert alert-danger'>✗ Failed to send email</div>";
                                echo "<p>Error: " . htmlspecialchars($mail2->ErrorInfo) . "</p>";
                            }
                        } else {
                            echo "<div class='alert alert-danger'>✗ <strong>Failed to connect to Mailtrap</strong></div>";
                            echo "<p>Error: " . htmlspecialchars($mail->ErrorInfo) . "</p>";
                        }
                    } catch (Exception $e) {
                        echo "<div class='alert alert-danger'>✗ <strong>Exception Error</strong></div>";
                        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                ?>
            </div>
        </div>

        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">← Back to ArtPortal</a>
        </div>
    </div>
</body>
</html>
