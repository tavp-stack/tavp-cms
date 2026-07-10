<?php

declare(strict_types=1);

namespace Tavp\Cms\Auth;

/**
 * RBAC-lite helper for the CMS admin.
 *
 * Maps allowed emails to roles. Each role has a set of permission patterns.
 * Patterns support wildcards: "content.*" matches "content.create", "content.edit", etc.
 */
class RbacGuard
{
    /** @var array<string,string> email => role */
    private array $roles;

    /** @var array<string,string[]> role => permissions */
    private array $permissions;

    /**
     * @param array<string,string> $roles
     * @param array<string,string[]> $permissions
     */
    public function __construct(array $roles, array $permissions)
    {
        $this->roles = $roles;
        $this->permissions = $permissions;
    }

    /**
     * Get the role for an email, defaulting to "editor".
     */
    public function role(string $email): string
    {
        return $this->roles[strtolower(trim($email))] ?? 'editor';
    }

    /**
     * Check if an email has a specific permission.
     */
    public function can(string $email, string $permission): bool
    {
        $role = $this->role($email);
        $perms = $this->permissions[$role] ?? [];

        foreach ($perms as $pattern) {
            if ($this->matches($pattern, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an email has any of the given permissions.
     */
    public function canAny(string $email, array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if ($this->can($email, $perm)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all permissions for an email.
     *
     * @return string[]
     */
    public function permissions(string $email): array
    {
        $role = $this->role($email);

        return $this->permissions[$role] ?? [];
    }

    private function matches(string $pattern, string $permission): bool
    {
        $pattern = rtrim($pattern, '*');
        return str_starts_with($permission, $pattern);
    }
}
