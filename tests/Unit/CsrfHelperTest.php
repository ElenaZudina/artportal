<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../helpers/CsrfHelper.php';

class CsrfHelperTest extends TestCase
{
    protected function setUp(): void
    {
        $_POST = [];
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_SESSION = [];
    }

    public function testTokenCreatesAndReusesSessionToken()
    {
        $firstToken = CsrfHelper::token();
        $secondToken = CsrfHelper::token();

        $this->assertSame($firstToken, $secondToken);
        $this->assertSame(64, strlen($firstToken));
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $firstToken);
    }

    public function testFieldContainsHiddenInputWithToken()
    {
        $token = CsrfHelper::token();
        $field = CsrfHelper::field();

        $this->assertStringContainsString('type="hidden"', $field);
        $this->assertStringContainsString('name="csrf_token"', $field);
        $this->assertStringContainsString('value="' . $token . '"', $field);
    }

    public function testValidateReturnsTrueForMatchingToken()
    {
        $token = CsrfHelper::token();
        $_POST['csrf_token'] = $token;

        $this->assertTrue(CsrfHelper::validate());
    }

    public function testValidateReturnsFalseWhenTokenIsMissing()
    {
        CsrfHelper::token();

        $this->assertFalse(CsrfHelper::validate());
    }

    public function testValidateReturnsFalseForWrongToken()
    {
        CsrfHelper::token();
        $_POST['csrf_token'] = str_repeat('0', 64);

        $this->assertFalse(CsrfHelper::validate());
    }
}
