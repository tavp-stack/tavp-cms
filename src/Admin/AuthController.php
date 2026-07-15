<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Auth\MailService;
use Tavp\Core\Http\Response;
use Tavp\Tavpid\Auth\OtpService;

/**
 * Admin auth — login/logout via session + OTP.
 */
class AuthController extends AdminController
{
    private OtpService $otp;

    public function __construct()
    {
        parent::__construct();
        $this->otp = new OtpService(
            (int) config('cms.admin.otp_ttl_minutes', 10),
            5, // max attempts
            6, // code length
        );
    }

    public function showLogin(): string|Response
    {
        if (!empty($_SESSION['cms_admin'])) {
            return $this->redirect('/admin');
        }

        // Clear pending OTP session
        unset($_SESSION['cms_otp']);

        return $this->partial('login', [
            'error' => null,
            'brand' => config('cms.admin.brand', 'TAVP'),
        ]);
    }

    public function sendOtp(): string|Response
    {
        $email = strtolower(trim((string) $this->request->input('email', '')));

        // Check if the email is allowed: either in the config allowlist
        // (built-in admins) or registered in the users table by an admin.
        $allowed = array_map('strtolower', (array) config('cms.admin.emails', []));
        if (!in_array($email, $allowed, true) && !$this->isRegisteredUser($email)) {
            return $this->partial('login', [
                'error' => 'That e-mail is not allowed to sign in.',
                'brand' => config('cms.admin.brand', 'TAVP'),
            ]);
        }

        // Generate OTP (tavpid returns array with code, hash, expires_at)
        $otpData = $this->otp->createOtp($email, 'email');

        $_SESSION['cms_otp'] = [
            'email' => $email,
            'hash' => $otpData['hash'],
            'expires' => $otpData['expires_at'],
        ];

        // Send the OTP email
        $this->sendOtpEmail($email, $otpData['code']);

        // Ensure session is saved before redirect
        session_write_close();

        return $this->redirect('/admin/verify');
    }

    /**
     * Whether an e-mail belongs to a user account managed in the database.
     */
    private function isRegisteredUser(string $email): bool
    {
        try {
            $rows = app('db')->fetchAll(
                'SELECT id FROM users WHERE email = :email LIMIT 1',
                \PDO::FETCH_ASSOC,
                ['email' => $email]
            );
            return !empty($rows);
        } catch (\Throwable) {
            return false;
        }
    }

    private function sendOtpEmail(string $email, string $code): void
    {
        try {
            $mailer = new MailService([
                'driver' => config('cms.mail.driver', 'smtp'),
                'host' => config('cms.mail.host', '127.0.0.1'),
                'port' => (int) config('cms.mail.port', 1025),
                'username' => config('cms.mail.username', ''),
                'password' => config('cms.mail.password', ''),
                'from' => config('cms.mail.from', 'noreply@tavp.web.id'),
            ]);

            $brand = config('cms.admin.brand', 'TAVP');
            $ttl = (int) config('cms.admin.otp_ttl_minutes', 10);

            $html = '<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
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

            $mailer->send(
                $email,
                "Your {$brand} sign-in code",
                "Your sign-in code is: {$code}\n\nIt expires in {$ttl} minutes.",
                $html
            );
        } catch (\Throwable) {
            // Email failed — don't block the flow
        }
    }

    public function showVerify(): string|Response
    {
        $otp = $_SESSION['cms_otp'] ?? null;

        if ($otp === null || ($otp['expires'] ?? 0) < time()) {
            return $this->redirect('/admin/login');
        }

        return $this->partial('verify', [
            'identifier' => $otp['email'] ?? '',
            'error' => null,
            'brand' => config('cms.admin.brand', 'TAVP'),
        ]);
    }

    public function verify(): string|Response
    {
        $code = (string) $this->request->input('code', '');
        $otp = $_SESSION['cms_otp'] ?? null;

        if ($otp === null || ($otp['expires'] ?? 0) < time()) {
            return $this->redirect('/admin/login');
        }

        // Verify OTP against session hash (tavpid API)
        $stored = [
            'hash' => $otp['hash'] ?? '',
            'expires_at' => $otp['expires'] ?? 0,
        ];

        if (!$this->otp->verifyOtp($code, $stored)) {
            return $this->partial('verify', [
                'identifier' => $otp['email'] ?? '',
                'error' => 'Invalid or expired code. Please try again.',
                'brand' => config('cms.admin.brand', 'TAVP'),
            ]);
        }

        // Login successful
        $_SESSION['cms_admin'] = $otp['email'];
        unset($_SESSION['cms_otp']);

        // Ensure session is saved before redirect
        session_write_close();

        return $this->redirect('/admin');
    }

    public function logout(): Response
    {
        unset($_SESSION['cms_admin'], $_SESSION['cms_otp']);

        return $this->redirect('/admin/login');
    }
}
