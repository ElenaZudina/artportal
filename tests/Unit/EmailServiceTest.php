<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/EmailService.php';

class EmailServiceTest extends TestCase
{
    /**
     * Tests that the HTML purchase request template includes escaped request fields.
     */
    public function testGetEmailTemplateContainsInterpolatedFields()
    {
        // The private HTML purchase template should include escaped request fields.
        $request = [
            'artist_name' => 'Ivan Artist',
            'artist_email' => 'ivan@example.com',
            'painting_title' => 'Sunrise Over Sea',
            'user_name' => 'Buyer',
            'user_email' => 'buyer@example.com',
            'id' => 77
        ];

        $ref = new ReflectionClass(EmailService::class);
        // Reflection keeps the template method private in production while still testable.
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

    /**
     * Tests that the plain text purchase request template includes request fields.
     */
    public function testGetPlainTextTemplateContainsFields()
    {
        // The plain text purchase template should include the main request fields.
        $request = [
            'artist_name' => 'Ivan Artist',
            'artist_email' => 'ivan@example.com',
            'painting_title' => 'Sunset',
            'user_name' => 'Buyer',
            'user_email' => 'buyer@example.com',
            'id' => 88
        ];

        $ref = new ReflectionClass(EmailService::class);
        // Reflection is used because the template builder is an internal helper.
        $method = $ref->getMethod('getPlainTextTemplate');
        $method->setAccessible(true);

        $text = $method->invoke(null, $request);

        $this->assertStringContainsString($request['painting_title'], $text);
        $this->assertStringContainsString($request['artist_name'], $text);
        $this->assertStringContainsString($request['user_email'], $text);
        $this->assertStringContainsString('#' . $request['id'], $text);
    }

    /**
     * Tests that the HTML password reset request template includes escaped user fields.
     */
    public function testGetPasswordResetRequestTemplateContainsEscapedUserFields()
    {
        // The private HTML password reset template should escape user-provided fields.
        $user = [
            'username' => '<Admin User>',
            'email' => 'user@example.com',
            'role' => 'user',
        ];

        $ref = new ReflectionClass(EmailService::class);
        $method = $ref->getMethod('getPasswordResetRequestTemplate');
        $method->setAccessible(true);

        $html = $method->invoke(null, $user);

        $this->assertStringContainsString('<html', $html);
        $this->assertStringContainsString('Password recovery request', $html);
        $this->assertStringContainsString(htmlspecialchars($user['username']), $html);
        $this->assertStringContainsString(htmlspecialchars($user['email']), $html);
        $this->assertStringContainsString(htmlspecialchars($user['role']), $html);
        $this->assertStringNotContainsString($user['username'], $html);
    }

    /**
     * Tests that the plain text password reset request template includes user fields.
     */
    public function testGetPasswordResetRequestPlainTextContainsFields()
    {
        // The plain text password reset template should include the main account fields.
        $user = [
            'username' => 'viewer',
            'email' => 'viewer@example.com',
            'role' => 'user',
        ];

        $ref = new ReflectionClass(EmailService::class);
        $method = $ref->getMethod('getPasswordResetRequestPlainText');
        $method->setAccessible(true);

        $text = $method->invoke(null, $user);

        $this->assertStringContainsString('Password recovery request', $text);
        $this->assertStringContainsString('Username: ' . $user['username'], $text);
        $this->assertStringContainsString('Email: ' . $user['email'], $text);
        $this->assertStringContainsString('Role: ' . $user['role'], $text);
    }

    /**
     * Tests that password reset templates use Unknown for missing user fields.
     */
    public function testPasswordResetTemplatesUseUnknownForMissingFields()
    {
        // Missing user fields should be represented as Unknown in both templates.
        $ref = new ReflectionClass(EmailService::class);
        $htmlMethod = $ref->getMethod('getPasswordResetRequestTemplate');
        $htmlMethod->setAccessible(true);
        $textMethod = $ref->getMethod('getPasswordResetRequestPlainText');
        $textMethod->setAccessible(true);

        $html = $htmlMethod->invoke(null, []);
        $text = $textMethod->invoke(null, []);

        $this->assertSame(3, substr_count($html, 'Unknown'));
        $this->assertStringContainsString('Username: Unknown', $text);
        $this->assertStringContainsString('Email: Unknown', $text);
        $this->assertStringContainsString('Role: Unknown', $text);
    }
}
