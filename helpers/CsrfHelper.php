<?php

/**
 * CSRF Protection Helper - prevents Cross-Site Request Forgery attacks
 * Generates, stores, and validates CSRF tokens for form submissions
 * Uses secure random tokens stored in session
 */
class CsrfHelper {
    private const TOKEN_KEY = 'csrf_token';
    private const FIELD_NAME = 'csrf_token';

    /**
     * Get or generate CSRF token for current session
     * Generates new token if none exists, stores in session
     * @return string Current CSRF token
     */
    public static function token(): string {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Generate HTML hidden input field with CSRF token
     * Use in forms to include token for server-side validation
     * @return string HTML hidden input element with CSRF token value
     */
    public static function field(): string {
        return '<input type="hidden" name="' . self::FIELD_NAME . '" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Validate CSRF token from form submission
     * Compares token from POST request against stored session token
     * Uses constant-time comparison to prevent timing attacks
     * @return bool True if token is valid, false otherwise
     */
    public static function validate(): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $sessionToken = $_SESSION[self::TOKEN_KEY] ?? '';
        $postToken = $_POST[self::FIELD_NAME] ?? '';

        return is_string($sessionToken)
            && is_string($postToken)
            && $sessionToken !== ''
            && hash_equals($sessionToken, $postToken);
    }
}
