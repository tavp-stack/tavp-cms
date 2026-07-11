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
    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }
        if (!$this->can('users.view') && !$this->can('users.*')) {
            return $this->redirect('/admin');
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
            return $this->redirect('/admin/users/create');
        }

        $now = date('Y-m-d H:i:s');
        $this->db()->execute(
            'INSERT INTO users (name, email, role, created_at, updated_at) '
            . 'VALUES (:name, :email, :role, :created_at, :updated_at)',
            [
                'name' => $name !== '' ? $name : $email,
                'email' => $email,
                'role' => $role,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $this->flash('success', 'User added. They can sign in with a one-time code.');
        return $this->redirect('/admin/users');
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
            return $this->redirect('/admin/users');
        }

        return $this->admin('users.form', [
            'user' => $rows[0],
            'roles' => $this->roles(),
            'action' => '/admin/users/' . $id,
            'heading' => 'Edit User',
            'isEdit' => true,
        ]);
    }

    public function update(string $id): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $name = trim((string) $this->request->input('name', ''));
        $role = (string) $this->request->input('role', 'editor');

        if (!in_array($role, $this->roles(), true)) {
            $role = 'editor';
        }

        $this->db()->execute(
            'UPDATE users SET name = :name, role = :role, updated_at = :updated_at WHERE id = :id',
            [
                'name' => $name,
                'role' => $role,
                'updated_at' => date('Y-m-d H:i:s'),
                'id' => $id,
            ]
        );

        $this->flash('success', 'User updated.');
        return $this->redirect('/admin/users');
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
            return $this->redirect('/admin/users');
        }

        $this->db()->execute('DELETE FROM users WHERE id = :id', ['id' => $id]);

        $this->flash('success', 'User removed.');
        return $this->redirect('/admin/users');
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
