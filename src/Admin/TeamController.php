<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Team management — list, create, manage team members.
 */
class TeamController extends AdminController
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

        $teams = $this->db()->query('SELECT * FROM teams ORDER BY name', []);

        return $this->admin('team.list', ['teams' => $teams]);
    }

    public function create(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        return $this->admin('team.form', [
            'team' => [],
            'action' => '/admin/teams',
            'heading' => 'New Team',
        ]);
    }

    public function store(): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $name = (string) $this->request->input('name', '');
        $ownerId = $_SESSION['tavpid_user_id'] ?? 0;

        $this->db()->insert('teams', [
            'name' => $name,
            'owner_id' => $ownerId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->redirect($this->adminPrefix() . '/teams');
    }

    public function edit(string $id): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $team = $this->db()->query('SELECT * FROM teams WHERE id = ?', [$id])[0] ?? null;
        $members = $this->db()->query(
            'SELECT tm.*, u.name, u.email FROM team_members tm LEFT JOIN users u ON tm.user_id = u.id WHERE tm.team_id = ?',
            [$id]
        );

        return $this->admin('team.form', [
            'team' => $team ?? [],
            'members' => $members,
            'action' => '/admin/teams/' . $id,
            'heading' => 'Edit Team: ' . ($team['name'] ?? ''),
        ]);
    }

    public function update(string $id): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $name = (string) $this->request->input('name', '');

        $this->db()->update('teams', [
            'name' => $name,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);

        return $this->redirect($this->adminPrefix() . '/teams');
    }

    public function destroy(string $id): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $this->db()->delete('team_members', ['team_id' => $id]);
        $this->db()->delete('teams', ['id' => $id]);

        return $this->redirect($this->adminPrefix() . '/teams');
    }

    public function addMember(string $teamId): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $email = (string) $this->request->input('email', '');
        $role = (string) $this->request->input('role', 'member');

        // Find user by email
        $user = $this->db()->query('SELECT id FROM users WHERE email = ?', [$email]);
        if (empty($user)) {
            $this->flash('error', 'User not found.');
            return $this->redirect($this->adminPrefix() . '/teams/' . $teamId . '/edit');
        }

        $userId = $user[0]['id'];
        $this->db()->insert('team_members', [
            'team_id' => $teamId,
            'user_id' => $userId,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->redirect($this->adminPrefix() . '/teams/' . $teamId . '/edit');
    }

    public function removeMember(string $teamId, string $memberId): Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $this->db()->delete('team_members', ['id' => $memberId, 'team_id' => $teamId]);

        return $this->redirect($this->adminPrefix() . '/teams/' . $teamId . '/edit');
    }

    private function db()
    {
        return app('db');
    }
}
