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

        $this->mailer()->send(
            $email,
            'Your ' . config('cms.admin.brand', 'TAVP') . ' sign-in code',
            "Your sign-in code is: {$code}\n\nIt expires in "
                . (int) config('cms.admin.otp_ttl_minutes', 10) . " minutes."
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
