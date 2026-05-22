<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../helpers/CsrfHelper.php';

class CsrfHelperTest extends TestCase
{
    /**
     * Prepares clean request and session state before each CSRF test.
     */
    protected function setUp(): void
    {
        // Reset request/session state so each CSRF test is isolated.
        $_POST = [];
        $_SESSION = [];
    }

    /**
     * Clears request and session state after each CSRF test.
     */
    protected function tearDown(): void
    {
        // Remove tokens and submitted values created during the test.
        $_POST = [];
        $_SESSION = [];
    }

    /**
     * Tests that CSRF token generation creates and reuses one session token.
     */
    public function testTokenCreatesAndReusesSessionToken()
    {
        // The helper should create one token and reuse it within the same session.
        $firstToken = CsrfHelper::token();
        $secondToken = CsrfHelper::token();

        $this->assertSame($firstToken, $secondToken);
        $this->assertSame(64, strlen($firstToken));
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $firstToken);
    }

    /**
     * Tests that the CSRF hidden field contains the current token.
     */
    public function testFieldContainsHiddenInputWithToken()
    {
        // The generated form field should include the current token as a hidden input.
        $token = CsrfHelper::token();
        $field = CsrfHelper::field();

        $this->assertStringContainsString('type="hidden"', $field);
        $this->assertStringContainsString('name="csrf_token"', $field);
        $this->assertStringContainsString('value="' . $token . '"', $field);
    }

    /**
     * Tests that validation passes when POST and session tokens match.
     */
    public function testValidateReturnsTrueForMatchingToken()
    {
        // A submitted token matching the session token should validate successfully.
        $token = CsrfHelper::token();
        $_POST['csrf_token'] = $token;

        $this->assertTrue(CsrfHelper::validate());
    }

    /**
     * Tests that validation fails when the POST token is missing.
     */
    public function testValidateReturnsFalseWhenTokenIsMissing()
    {
        // Missing POST token should fail even when the session token exists.
        CsrfHelper::token();

        $this->assertFalse(CsrfHelper::validate());
    }

    /**
     * Tests that validation fails when the POST token has the wrong value.
     */
    public function testValidateReturnsFalseForWrongToken()
    {
        // A submitted token with the wrong value should fail validation.
        CsrfHelper::token();
        $_POST['csrf_token'] = str_repeat('0', 64);

        $this->assertFalse(CsrfHelper::validate());
    }
}
