<?php

declare(strict_types=1);

namespace Tavp\Cms\Admin;

use Tavp\Core\Http\Response;

/**
 * Admin full-text search.
 */
class SearchController extends AdminController
{
    public function search(): string|Response
    {
        if ($r = $this->guard()) {
            return $r;
        }

        $query = (string) $this->request->input('q', '');
        $results = [];

        if ($query !== '') {
            $search = app()->getService(\Tavp\Cms\Search\SearchEngine::class);
            $results = $search->search($query, limit: 50);
        }

        return $this->admin('search', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
