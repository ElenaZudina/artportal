<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class EmailService {
    
    /**
     * Отправляет email уведомление художнику о новом запросе на покупку
     */
    public static function sendPurchaseRequestNotification($request) {
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            error_log('PHPMailer is not installed or could not be autoloaded. Skipping purchase request email.');
            return false;
        }

        $mail = new PHPMailer(true);
        
        try {
            // Конфигурация Mailtrap
            $mail->isSMTP();
            $mail->Host = $_ENV['MAILTRAP_HOST'] ?? 'sandbox.smtp.mailtrap.io';
            $mail->Port = $_ENV['MAILTRAP_PORT'] ?? 587;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Username = $_ENV['MAILTRAP_USERNAME'] ?? '';
            $mail->Password = $_ENV['MAILTRAP_PASSWORD'] ?? '';
            
            // Отключить проверку сертификата SSL (для локального тестирования)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Отправитель (любой email для Mailtrap)
            $mail->setFrom('noreply@artportal.local', 'ArtPortal');
            
            // Получатель (художник)
            $mail->addAddress($request['artist_email'], $request['artist_name']);
            
            // Тема и содержание
            $mail->isHTML(true);
            $mail->Subject = 'New Purchase Request: ' . htmlspecialchars($request['painting_title']);
            $mail->Body = self::getEmailTemplate($request);
            $mail->AltBody = self::getPlainTextTemplate($request);
            
            $result = $mail->send();
            return $result;
            
        } catch (\Throwable $e) {
            error_log('PHPMailer Exception: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * HTML шаблон письма
     */
    private static function getEmailTemplate($request) {
        $artistDashboardLink = 'http://localhost/artportal/dashboard'; // Измените на ваш URL
        
        return "
        <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #f5f5f5; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
                    .content { background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                    .footer { margin-top: 20px; font-size: 12px; color: #777; }
                    .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
                    hr { border: none; border-top: 1px solid #ddd; margin: 20px 0; }
                    .painting-info { background: #f9f9f9; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0; }
                    .user-info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1 style='margin: 0; color: #333;'>ArtPortal</h1>
                        <p style='margin: 5px 0 0 0; color: #777;'>New Purchase Request</p>
                    </div>
                    
                    <div class='content'>
                        <p>Hi <strong>" . htmlspecialchars($request['artist_name']) . "</strong>,</p>
                        
                        <p>Good news! A buyer has expressed interest in your artwork:</p>
                        
                        <div class='painting-info'>
                            <h3 style='margin-top: 0;'>" . htmlspecialchars($request['painting_title']) . "</h3>
                        </div>
                        
                        <p><strong>Buyer Information:</strong></p>
                        <div class='user-info'>
                            <p><strong>Name:</strong> " . htmlspecialchars($request['user_name']) . "</p>
                            <p><strong>Email:</strong> <a href='mailto:" . htmlspecialchars($request['user_email']) . "'>" . htmlspecialchars($request['user_email']) . "</a></p>
                        </div>
                        
                        <p><strong>Request ID:</strong> <code>#" . $request['id'] . "</code></p>
                        
                        <p>You can review this request and contact the buyer to discuss the details.</p>
                        
                        <a href='" . $artistDashboardLink . "' class='button'>View Request in Dashboard</a>
                        
                        <hr>
                        
                        <p>Best regards,<br><strong>ArtPortal Team</strong></p>
                    </div>
                    
                    <div class='footer'>
                        <p>This is an automated message. Please do not reply directly to this email.</p>
                    </div>
                </div>
            </body>
        </html>
        ";
    }
    
    /**
     * Простой текстовый шаблон
     */
    private static function getPlainTextTemplate($request) {
        return "
New Purchase Request

Hi " . $request['artist_name'] . ",

A buyer has expressed interest in your artwork: " . $request['painting_title'] . "

Buyer Information:
Name: " . $request['user_name'] . "
Email: " . $request['user_email'] . "

Request ID: #" . $request['id'] . "

You can review this request and contact the buyer to discuss the details.

Best regards,
ArtPortal Team
        ";
    }
}
?>