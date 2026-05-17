<?php

class CsrfHelper {
    private const TOKEN_KEY = 'csrf_token';
    private const FIELD_NAME = 'csrf_token';

    public static function token(): string {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::TOKEN_KEY];
    }

    public static function field(): string {
        return '<input type="hidden" name="' . self::FIELD_NAME . '" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

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
