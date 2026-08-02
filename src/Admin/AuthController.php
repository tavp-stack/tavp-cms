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

    protected function adminPrefix(): string
    {
        $dbPrefix = null;
        try {
            $settings = app()->getService(\Tavp\Cms\Settings\Settings::class);
            $dbPrefix = $settings?->get('admin.route_prefix');
        } catch (\Throwable) {}
        return '/' . trim($dbPrefix ?: config('cms.admin.route_prefix', 'admin'), '/');
    }

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
            return $this->redirect($this->adminPrefix());
        }

        // Clear pending OTP session
        unset($_SESSION['cms_otp']);

        return $this->partial('login', [
            'error' => null,
            'brand' => config('cms.admin.brand', 'TAVP'),
            'adminPrefix' => $this->adminPrefix(),
        ]);
    }

    public function sendOtp(): string|Response
    {
        $email = strtolower(trim((string) $this->request->input('email', '')));

        // No e-mail supplied — return JSON error for AJAX or redirect for form
        if ($email === '') {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Email diperlukan.']);
            }
            return $this->showLogin();
        }

        // Validate captcha before proceeding
        if (!captcha_verify()) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Captcha salah. Silakan coba lagi.']);
            }
            return $this->partial('login', [
                'error' => 'Captcha salah. Silakan coba lagi.',
                'brand' => config('cms.admin.brand', 'TAVP'),
                'adminPrefix' => $this->adminPrefix(),
            ]);
        }

        // Check if the email is allowed: either in the config allowlist
        // (built-in admins) or registered in the users table by an admin.
        $allowed = array_map('strtolower', (array) config('cms.admin.emails', []));
        if (!in_array($email, $allowed, true) && !$this->isRegisteredUser($email)) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Email tidak terdaftar.']);
            }
            return $this->partial('login', [
                'error' => 'That e-mail is not allowed to sign in.',
                'brand' => config('cms.admin.brand', 'TAVP'),
                'adminPrefix' => $this->adminPrefix(),
            ]);
        }

        // Generate OTP (tavpid returns array with code, hash, expires_at)
        $otpData = $this->otp->createOtp($email, 'email');

        $_SESSION['cms_otp'] = [
            'email' => $email,
            'hash' => $otpData['hash'],
            'expires' => $otpData['expires_at'],
        ];

        // Fire-and-forget: send OTP email, catch errors silently
        try {
            $this->sendOtpEmail($email, $otpData['code']);
        } catch (\Throwable $e) {
            error_log('[TAVP CMS] OTP email failed: ' . $e->getMessage());
        }

        // AJAX: return JSON immediately
        if ($this->isAjax()) {
            session_write_close();
            return $this->json(['success' => true, 'message' => 'Kode OTP telah dikirim.']);
        }

        // Form fallback: redirect to verify page
        session_write_close();
        return $this->redirect($this->adminPrefix() . '/verify');
    }

    /**
     * Whether the request is an AJAX/fetch request.
     */
    private function isAjax(): bool
    {
        $header = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return strtolower($header) === 'xmlhttprequest'
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    /**
     * Return a JSON response.
     */
    protected function json(mixed $data, int $status = 200): Response
    {
        $response = new Response();
        $response->setStatusCode($status);
        $response->header('Content-Type', 'application/json');
        $response->setContent(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response;
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

    private function sendOtpEmail(string $email, string $code): bool
    {
        try {
            $mailer = new MailService(config('cms.mail'));

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

            return $mailer->send(
                $email,
                "Your {$brand} sign-in code",
                "Your sign-in code is: {$code}\n\nIt expires in {$ttl} minutes.",
                $html
            );
        } catch (\Throwable $e) {
            // Log the error for debugging
            error_log('[TAVP CMS] OTP email failed: ' . $e->getMessage());

            // Store error in session for user feedback
            $_SESSION['cms_otp_error'] = $e->getMessage();

            return false;
        }
    }

    public function showVerify(): string|Response
    {
        $otp = $_SESSION['cms_otp'] ?? null;

        if ($otp === null || ($otp['expires'] ?? 0) < time()) {
            return $this->redirect($this->adminPrefix() . '/login');
        }

        return $this->partial('verify', [
            'identifier' => $otp['email'] ?? '',
            'error' => null,
            'brand' => config('cms.admin.brand', 'TAVP'),
            'adminPrefix' => $this->adminPrefix(),
        ]);
    }

    public function verify(): string|Response
    {
        $code = (string) $this->request->input('code', '');
        $otp = $_SESSION['cms_otp'] ?? null;

        if ($otp === null || ($otp['expires'] ?? 0) < time()) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Kode kadaluarsa. Silakan minta kode baru.']);
            }
            return $this->redirect($this->adminPrefix() . '/login');
        }

        // Verify OTP against session hash (tavpid API)
        $stored = [
            'hash' => $otp['hash'] ?? '',
            'expires_at' => $otp['expires'] ?? 0,
        ];

        if (!$this->otp->verifyOtp($code, $stored)) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Kode salah atau kadaluarsa.']);
            }
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

        if ($this->isAjax()) {
            return $this->json(['success' => true, 'redirect' => $this->adminPrefix()]);
        }

        return $this->redirect($this->adminPrefix());
    }

    public function logout(): Response
    {
        unset($_SESSION['cms_admin'], $_SESSION['cms_otp']);

        return $this->redirect($this->adminPrefix() . '/login');
    }
}
