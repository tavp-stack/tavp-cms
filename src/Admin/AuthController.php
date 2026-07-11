<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;
use Tavp\Tavpid\Auth\SessionAuth;

/**
 * Admin auth — login/logout via tavpid SessionAuth.
 */
class AuthController extends AdminController
{
    public function showLogin(): string|Response
    {
        if ($this->sessionAuth !== null && $this->sessionAuth->check()) {
            return $this->redirect('/admin');
        }

        // Clear pending OTP session when going back to login
        if ($this->sessionAuth !== null) {
            $this->sessionAuth->clearPending();
        }

        return $this->partial('login', [
            'error' => null,
            'brand' => config('cms.admin.brand', 'TAVP'),
        ]);
    }

    public function sendOtp(): string|Response
    {
        $email = (string) $this->request->input('email', '');

        if ($this->sessionAuth === null) {
            return $this->redirect('/admin/login');
        }

        $result = $this->sessionAuth->requestCode($email);

        if ($result === false) {
            return $this->partial('login', [
                'error' => 'That e-mail is not allowed to sign in.',
                'brand' => config('cms.admin.brand', 'TAVP'),
            ]);
        }

        // Send the OTP email
        $this->sendOtpEmail($email, $result['code']);

        return $this->redirect('/admin/verify');
    }

    private function sendOtpEmail(string $email, string $code): void
    {
        try {
            $mailer = new \Tavp\Core\Auth\MailService([
                'driver' => config('cms.mail.driver', 'smtp'),
                'host' => config('cms.mail.host', '127.0.0.1'),
                'port' => (int) config('cms.mail.port', 1025),
                'username' => config('cms.mail.username', ''),
                'password' => config('cms.mail.password', ''),
                'from' => config('cms.mail.from', 'noreply@tavp.web.id'),
            ]);

            $brand = config('cms.admin.brand', 'TAVP');
            $ttl = (int) config('cms.admin.otp_ttl_minutes', 10);

            $mailer->send(
                $email,
                "Your {$brand} sign-in code",
                "Your sign-in code is: {$code}\n\nIt expires in {$ttl} minutes."
            );
        } catch (\Throwable) {
            // Email failed — don't block the flow
        }
    }

    public function showVerify(): string|Response
    {
        $identifier = $this->sessionAuth?->pendingIdentifier();

        if ($identifier === null) {
            return $this->redirect('/admin/login');
        }

        return $this->partial('verify', [
            'identifier' => $identifier,
            'error' => null,
            'brand' => config('cms.admin.brand', 'TAVP'),
        ]);
    }

    public function verify(): string|Response
    {
        $code = (string) $this->request->input('code', '');

        if ($this->sessionAuth === null || !$this->sessionAuth->verify($code)) {
            return $this->partial('verify', [
                'identifier' => $this->sessionAuth?->pendingIdentifier() ?? '',
                'error' => 'Invalid or expired code. Please try again.',
                'brand' => config('cms.admin.brand', 'TAVP'),
            ]);
        }

        return $this->redirect('/admin');
    }

    public function logout(): Response
    {
        $this->sessionAuth?->logout();

        return $this->redirect('/admin/login');
    }
}
