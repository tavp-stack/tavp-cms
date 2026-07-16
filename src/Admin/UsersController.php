<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * User management — list, create, edit and delete admin/editor accounts.
 *
 * Accounts live in the `users` table. Each user has a role that governs
 * their permissions (see config cms.admin.permissions). Users sign in with
 * a one-time code sent to their e-mail — no password is stored.
 */
class UsersController extends AdminController
{
    protected function adminPrefix(): string
    {
        $dbPrefix = null;
        try {
            $settings = app()->getService(\Tavp\Cms\Settings\Settings::class);
            $dbPrefix = $settings?->get('admin.route_prefix');
        } catch (\Throwable) {}
        return '/' . trim($dbPrefix ?: config('cms.admin.route_prefix', 'admin'), '/');
    }

    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }
        if (!$this->can('users.view') && !$this->can('users.*')) {
            return $this->redirect($this->adminPrefix());
        }

        // Sorting
        $allowedSort = ['name', 'email', 'role', 'created_at'];
        $sort = $this->request->input('sort', 'created_at');
        $dir = strtoupper((string) $this->request->input('dir', 'DESC'));
        if (!in_array($sort, $allowedSort, true)) { $sort = 'created_at'; }
        if (!in_array($dir, ['ASC', 'DESC'], true)) { $dir = 'DESC'; }

        $users = $this->db()->fetchAll(
            "SELECT * FROM users ORDER BY {$sort} {$dir}",
            \PDO::FETCH_ASSOC
        );

        return $this->admin('users.list', [
            'users' => $users,
            'currentEmail' => strtolower((string) $this->adminUser()),
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function create(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        return $this->admin('users.form', [
            'user' => [],
            'roles' => $this->roles(),
            'action' => '/admin/users',
            'heading' => 'New User',
            'isEdit' => false,
        ]);
    }

    public function store(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $name = trim((string) $this->request->input('name', ''));
        $email = strtolower(trim((string) $this->request->input('email', '')));
        $role = (string) $this->request->input('role', 'editor');

        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'A valid e-mail is required.';
        }
        if (!in_array($role, $this->roles(), true)) {
            $errors['role'][] = 'Invalid role.';
        }
        if (empty($errors)) {
            $existing = $this->db()->fetchAll(
                'SELECT id FROM users WHERE email = :email LIMIT 1',
                \PDO::FETCH_ASSOC,
                ['email' => $email]
            );
            if (!empty($existing)) {
                $errors['email'][] = 'A user with that e-mail already exists.';
            }
        }

        if (!empty($errors)) {
            $this->flashErrors($errors);
            $this->flashOld(['name' => $name, 'email' => $email, 'role' => $role]);
            return $this->redirect($this->adminPrefix() . '/users/create');
        }

        $now = date('Y-m-d H:i:s');
        $this->db()->execute(
            'INSERT INTO users (name, email, bio, social_github, social_twitter, social_linkedin, social_instagram, social_website, created_at, updated_at) '
            . 'VALUES (:name, :email, :bio, :social_github, :social_twitter, :social_linkedin, :social_instagram, :social_website, :created_at, :updated_at)',
            [
                'name' => $name !== '' ? $name : $email,
                'email' => $email,
                'bio' => trim((string) $this->request->input('bio', '')),
                'social_github' => trim((string) $this->request->input('social_github', '')),
                'social_twitter' => trim((string) $this->request->input('social_twitter', '')),
                'social_linkedin' => trim((string) $this->request->input('social_linkedin', '')),
                'social_instagram' => trim((string) $this->request->input('social_instagram', '')),
                'social_website' => trim((string) $this->request->input('social_website', '')),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // Assign role in user_roles table
        try {
            $newUserId = $this->db()->lastInsertId();
            $roleRow = $this->db()->fetchAll(
                'SELECT id FROM roles WHERE name = :role LIMIT 1',
                \PDO::FETCH_ASSOC,
                ['role' => $role]
            );
            if (!empty($roleRow)) {
                $this->db()->execute(
                    'INSERT INTO user_roles (user_id, role_id, created_at) VALUES (:uid, :rid, NOW())',
                    ['uid' => $newUserId, 'rid' => $roleRow[0]['id']]
                );
            }
        } catch (\Throwable) {
            // roles table might not exist — ignore
        }

        $this->flash('success', 'User added. They can sign in with a one-time code.');
        return $this->redirect($this->adminPrefix() . '/users');
    }

    public function edit(string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $rows = $this->db()->fetchAll(
            'SELECT * FROM users WHERE id = :id LIMIT 1',
            \PDO::FETCH_ASSOC,
            ['id' => $id]
        );
        if (empty($rows)) {
            return $this->redirect($this->adminPrefix() . '/users');
        }

        return $this->admin('users.form', [
            'user' => $rows[0],
            'roles' => $this->roles(),
            'action' => '/admin/users/' . $id,
            'heading' => 'Edit User',
            'isEdit' => true,
            'isAdmin' => $this->can('users.*'),
        ]);
    }

    public function update(string $id): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $name = trim((string) $this->request->input('name', ''));
        $email = strtolower(trim((string) $this->request->input('email', '')));
        $role = (string) $this->request->input('role', 'editor');

        if (!in_array($role, $this->roles(), true)) {
            $role = 'editor';
        }

        // Validate email
        $errors = [];
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'A valid e-mail is required.';
        }

        // Check if email is already taken by another user
        if (empty($errors)) {
            $existing = $this->db()->fetchAll(
                'SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1',
                \PDO::FETCH_ASSOC,
                ['email' => $email, 'id' => $id]
            );
            if (!empty($existing)) {
                $errors['email'][] = 'That e-mail is already taken.';
            }
        }

        if (!empty($errors)) {
            $this->flashErrors($errors);
            return $this->redirect($this->adminPrefix() . '/users/' . $id . '/edit');
        }

        $this->db()->execute(
            'UPDATE users SET name = :name, email = :email, bio = :bio, social_github = :social_github, social_twitter = :social_twitter, social_linkedin = :social_linkedin, social_instagram = :social_instagram, social_website = :social_website, updated_at = :updated_at WHERE id = :id',
            [
                'name' => $name,
                'email' => $email,
                'bio' => trim((string) $this->request->input('bio', '')),
                'social_github' => trim((string) $this->request->input('social_github', '')),
                'social_twitter' => trim((string) $this->request->input('social_twitter', '')),
                'social_linkedin' => trim((string) $this->request->input('social_linkedin', '')),
                'social_instagram' => trim((string) $this->request->input('social_instagram', '')),
                'social_website' => trim((string) $this->request->input('social_website', '')),
                'updated_at' => date('Y-m-d H:i:s'),
                'id' => $id,
            ]
        );

        // Update role in user_roles table
        try {
            $roleRow = $this->db()->fetchAll(
                'SELECT id FROM roles WHERE name = :role LIMIT 1',
                \PDO::FETCH_ASSOC,
                ['role' => $role]
            );
            if (!empty($roleRow)) {
                $roleId = $roleRow[0]['id'];
                $this->db()->execute('DELETE FROM user_roles WHERE user_id = :uid', ['uid' => $id]);
                $this->db()->execute(
                    'INSERT INTO user_roles (user_id, role_id, created_at) VALUES (:uid, :rid, NOW())',
                    ['uid' => $id, 'rid' => $roleId]
                );
            }
        } catch (\Throwable) {
            // roles table might not exist — ignore
        }

        $this->flash('success', 'User updated.');
        return $this->redirect($this->adminPrefix() . '/users');
    }

    public function destroy(string $id): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $rows = $this->db()->fetchAll(
            'SELECT email FROM users WHERE id = :id LIMIT 1',
            \PDO::FETCH_ASSOC,
            ['id' => $id]
        );
        $email = strtolower((string) ($rows[0]['email'] ?? ''));

        // Never let an admin delete their own account or the built-in admin.
        $protected = array_map('strtolower', (array) config('cms.admin.emails', []));
        if ($email === strtolower((string) $this->adminUser()) || in_array($email, $protected, true)) {
            $this->flash('error', 'That account cannot be deleted.');
            return $this->redirect($this->adminPrefix() . '/users');
        }

        $this->db()->execute('DELETE FROM users WHERE id = :id', ['id' => $id]);

        $this->flash('success', 'User removed.');
        return $this->redirect($this->adminPrefix() . '/users');
    }

    /**
     * @return array<int,string>
     */
    private function roles(): array
    {
        $roles = array_keys((array) config('cms.admin.permissions', []));
        return !empty($roles) ? $roles : ['admin', 'editor'];
    }

    private function db()
    {
        return app('db');
    }
}
