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

        return $this->admin('dashboard', ['counts' => $this->counts()]);
    }

    public function home(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        return $this->admin('home', ['counts' => $this->counts()]);
    }

    /**
     * @return array<string,int>
     */
    private function counts(): array
    {
        $counts = [];
        foreach ($this->bread()->types() as $name => $type) {
            $counts[$name] = count($this->bread()->browse($name));
        }

        return $counts;
    }
}
