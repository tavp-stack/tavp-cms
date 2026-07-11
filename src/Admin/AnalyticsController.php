<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Analytics dashboard controller.
 */
class AnalyticsController extends AdminController
{
    public function index(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        return $this->admin('analytics', []);
    }
}
