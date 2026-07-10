<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

class DashboardController extends AdminController
{
    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $counts = [];
        foreach ($this->bread()->types() as $name => $type) {
            $counts[$name] = count($this->bread()->browse($name));
        }

        return $this->admin('dashboard', ['counts' => $counts]);
    }
}
