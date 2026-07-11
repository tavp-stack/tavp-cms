<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Auth\MailService;
use Tavp\Tavpid\Auth\OtpService;

/**
 * Passwordless admin authentication via email OTP.
 *
 * The flow is intentionally thin and session-based:
 *   request code -> email (via Mailpit/SMTP in dev) -> verify -> logged in.
 * Only e-mails on the configured allow-list may sign in.
 */
class AdminAuth
{
    private OtpService $otp;

    public function __construct()
    {
        $this->ensureSession();
        $this->otp = new OtpService(
            (int) config('cms.admin.otp_ttl_minutes', 10),
        );
    }

    /**
     * Is the given e-mail allowed to administer the site?
     */
    public function isAllowed(string $email): bool
    {
        $allow = array_map('strtolower', (array) config('cms.admin.emails', []));

        return in_array(strtolower(trim($email)), $allow, true);
    }

    /**
     * Generate a code, remember it in the session, and e-mail it.
     * Returns false when the e-mail is not allowed.
     */
    public function requestCode(string $email): bool
    {
        $email = strtolower(trim($email));

        if (!$this->isAllowed($email)) {
            return false;
        }

        $code = $this->otp->createOtp($email, 'email');

        $_SESSION['cms_otp'] = [
            'email' => $email,
            'hash' => $this->otp->hash($code),
            'expires' => time() + (int) config('cms.admin.otp_ttl_minutes', 10) * 60,
        ];

        $brand = config('cms.admin.brand', 'TAVP');
        $ttl = (int) config('cms.admin.otp_ttl_minutes', 10);

        $html = '<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body { margin: 0; padding: 0; background-color: #0d131f; font-family: Inter, system-ui, sans-serif; }
  .container { max-width: 480px; margin: 0 auto; padding: 40px 24px; }
  .card { background-color: #1a202c; border: 1px solid #45474c; border-radius: 0.5rem; padding: 32px; }
  .code { font-family: JetBrains Mono, monospace; font-size: 32px; font-weight: 600; color: #e6c446; letter-spacing: 0.1em; text-align: center; padding: 24px 0; }
</style>
</head>
<body>
<div class="container">
  <div style="text-align: center; margin-bottom: 32px;">
    <span style="font-size: 24px; font-weight: 700; color: #e6c446;">' . $brand . '</span>
    <span style="font-size: 14px; color: #8f9097; margin-left: 8px;">admin</span>
  </div>
  <div class="card">
    <h1 style="color: #dde2f3; font-size: 20px; font-weight: 600; margin: 0 0 8px 0;">Sign-in Code</h1>
    <p style="color: #8f9097; font-size: 14px; margin: 0 0 24px 0;">Use the code below to sign in to your admin panel.</p>
    <div class="code">' . $code . '</div>
    <p style="color: #8f9097; font-size: 12px; text-align: center; margin: 16px 0 0 0;">This code expires in ' . $ttl . ' minutes.</p>
  </div>
  <p style="color: #45474c; font-size: 12px; text-align: center; margin-top: 24px;">If you did not request this code, you can safely ignore this email.</p>
</div>
</body>
</html>';

        $this->mailer()->send(
            $email,
            "Your {$brand} sign-in code",
            "Your sign-in code is: {$code}\n\nIt expires in {$ttl} minutes.",
            $html
        );

        return true;
    }

    /**
     * Verify a submitted code and, on success, start an admin session.
     */
    public function verify(string $code): bool
    {
        $otp = $_SESSION['cms_otp'] ?? null;

        if (!is_array($otp) || ($otp['expires'] ?? 0) < time()) {
            return false;
        }

        if (!$this->otp->verifyOtp(trim($code), $otp['hash'])) {
            return false;
        }

        $_SESSION['cms_admin'] = $otp['email'];
        unset($_SESSION['cms_otp']);

        return true;
    }

    public function pendingEmail(): ?string
    {
        return $_SESSION['cms_otp']['email'] ?? null;
    }

    public function check(): bool
    {
        return !empty($_SESSION['cms_admin']);
    }

    public function user(): ?string
    {
        return $_SESSION['cms_admin'] ?? null;
    }

    public function logout(): void
    {
        unset($_SESSION['cms_admin'], $_SESSION['cms_otp']);
    }

    private function mailer(): MailService
    {
        return new MailService([
            'driver' => config('cms.mail.driver', 'smtp'),
            'host' => config('cms.mail.host', '127.0.0.1'),
            'port' => (int) config('cms.mail.port', 1025),
            'username' => config('cms.mail.username', ''),
            'password' => config('cms.mail.password', ''),
            'from' => config('cms.mail.from', 'noreply@tavp.web.id'),
        ]);
    }

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
