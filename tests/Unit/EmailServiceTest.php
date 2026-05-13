<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/EmailService.php';

class EmailServiceTest extends TestCase
{
    public function testGetEmailTemplateContainsInterpolatedFields()
    {
        $request = [
            'artist_name' => 'Ivan Artist',
            'artist_email' => 'ivan@example.com',
            'painting_title' => 'Sunrise Over Sea',
            'user_name' => 'Buyer',
            'user_email' => 'buyer@example.com',
            'id' => 77
        ];

        $ref = new ReflectionClass(EmailService::class);
        $method = $ref->getMethod('getEmailTemplate');
        $method->setAccessible(true);

        $html = $method->invoke(null, $request);

        $this->assertStringContainsString('<html', $html);
        $this->assertStringContainsString('ArtPortal', $html);
        $this->assertStringContainsString(htmlspecialchars($request['painting_title']), $html);
        $this->assertStringContainsString(htmlspecialchars($request['artist_name']), $html);
        $this->assertStringContainsString(htmlspecialchars($request['user_email']), $html);
        $this->assertStringContainsString('#' . $request['id'], $html);
    }

    public function testGetPlainTextTemplateContainsFields()
    {
        $request = [
            'artist_name' => 'Ivan Artist',
            'artist_email' => 'ivan@example.com',
            'painting_title' => 'Sunset',
            'user_name' => 'Buyer',
            'user_email' => 'buyer@example.com',
            'id' => 88
        ];

        $ref = new ReflectionClass(EmailService::class);
        $method = $ref->getMethod('getPlainTextTemplate');
        $method->setAccessible(true);

        $text = $method->invoke(null, $request);

        $this->assertStringContainsString($request['painting_title'], $text);
        $this->assertStringContainsString($request['artist_name'], $text);
        $this->assertStringContainsString($request['user_email'], $text);
        $this->assertStringContainsString('#' . $request['id'], $text);
    }
}
