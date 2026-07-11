<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * BREAD Manager — admin-only content type management.
 */
class BreadController extends AdminController
{
    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        // Only admin can access BREAD manager
        $email = $this->adminUser();
        if ($this->rbac !== null && $email !== null) {
            $role = $this->rbac->role($email);
            if ($role !== 'admin') {
                return $this->redirect('/admin');
            }
        }

        $types = $this->bread()->types();
        $counts = [];
        foreach ($types as $name => $type) {
            $counts[$name] = count($this->bread()->browse($name));
        }

        return $this->admin('bread', [
            'types' => $types,
            'counts' => $counts,
        ]);
    }
}
