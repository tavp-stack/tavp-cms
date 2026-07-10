<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Admin sign-in via email OTP.
 */
class AuthController extends AdminController
{
    public function showLogin(): string|Response
    {
        if ($this->auth->check()) {
            return $this->redirect('/admin');
        }

        return $this->partial('login', ['error' => null, 'brand' => config('cms.admin.brand', 'TAVP')]);
    }

    public function sendOtp(): string|Response
    {
        $email = (string) $this->request->input('email', '');

        if (!$this->auth->requestCode($email)) {
            return $this->partial('login', [
                'error' => 'That e-mail is not allowed to sign in.',
                'brand' => config('cms.admin.brand', 'TAVP'),
            ]);
        }

        return $this->redirect('/admin/verify');
    }

    public function showVerify(): string|Response
    {
        $email = $this->auth->pendingEmail();

        if ($email === null) {
            return $this->redirect('/admin/login');
        }

        return $this->partial('verify', [
            'email' => $email,
            'error' => null,
            'brand' => config('cms.admin.brand', 'TAVP'),
        ]);
    }

    public function verify(): string|Response
    {
        $code = (string) $this->request->input('code', '');

        if (!$this->auth->verify($code)) {
            return $this->partial('verify', [
                'email' => $this->auth->pendingEmail() ?? '',
                'error' => 'Invalid or expired code. Please try again.',
                'brand' => config('cms.admin.brand', 'TAVP'),
            ]);
        }

        return $this->redirect('/admin');
    }

    public function logout(): Response
    {
        $this->auth->logout();

        return $this->redirect('/admin/login');
    }
}
