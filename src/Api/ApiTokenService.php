<?php

declare(strict_types=1);

namespace Tavp\Cms\Api;

/**
 * API token authentication.
 *
 * Checks bearer tokens against config list + a tokens file.
 * Tokens are SHA-256 hashed; the file stores hashed tokens.
 */
class ApiTokenService
{
    /** @var string[] */
    private array $tokens = [];

    public function __construct(
        private readonly array $configTokens = [],
        private readonly ?string $tokensFile = null,
    ) {
        $this->tokens = $configTokens;
        $this->loadFile();
    }

    /**
     * Verify a bearer token.
     */
    public function verify(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        $hashed = hash('sha256', $token);

        foreach ($this->tokens as $stored) {
            if (hash_equals(trim($stored), $hashed) || hash_equals(trim($stored), $token)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a new token, store it, and return the plain text.
     */
    public function generate(string $name = 'default'): string
    {
        $plain = bin2hex(random_bytes(32));
        $hashed = hash('sha256', $plain);

        $this->tokens[] = $hashed;
        $this->saveFile();

        return $plain;
    }

    /**
     * Revoke a token (by index).
     */
    public function revoke(int $index): bool
    {
        if (!isset($this->tokens[$index])) {
            return false;
        }

        array_splice($this->tokens, $index, 1);
        $this->saveFile();

        return true;
    }

    /**
     * List hashed tokens (never expose plain).
     *
     * @return string[]
     */
    public function list(): array
    {
        return $this->tokens;
    }

    /**
     * Extract bearer token from Authorization header.
     */
    public static function extractToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

        if (preg_match('/^Bearer\s+(.+)$/i', $header, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    private function loadFile(): void
    {
        if ($this->tokensFile === null || !is_file($this->tokensFile)) {
            return;
        }

        $lines = file($this->tokensFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line !== '' && !in_array($line, $this->tokens, true)) {
                $this->tokens[] = $line;
            }
        }
    }

    private function saveFile(): void
    {
        if ($this->tokensFile === null) {
            return;
        }

        $dir = dirname($this->tokensFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($this->tokensFile, implode("\n", $this->tokens) . "\n");
    }
}
