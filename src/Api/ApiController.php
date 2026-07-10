<?php

declare(strict_types=1);

namespace Tavp\Cms\Api;

use Tavp\Cms\Bread\BreadManager;
use Tavp\Cms\Taxonomy\TaxonomyManager;
use Tavp\Core\Controllers\BaseController;
use Tavp\Core\Http\Response;

/**
 * Headless REST API controller.
 *
 * Endpoints:
 *   GET    /api/cms/types                       — list content types
 *   GET    /api/cms/{type}                      — browse records (paginated)
 *   GET    /api/cms/{type}/{id}                 — read a single record
 *   POST   /api/cms/{type}                      — create a record
 *   PUT    /api/cms/{type}/{id}                 — update a record
 *   DELETE /api/cms/{type}/{id}                 — delete a record
 *   GET    /api/cms/search?q=...                — full-text search
 *   GET    /api/cms/{type}/{id}/revisions       — revision history
 *   POST   /api/cms/{type}/{id}/rollback/{ts}   — rollback to revision
 *   GET    /api/cms/taxonomy/{type}             — list taxonomy terms
 *   POST   /api/cms/taxonomy                    — create taxonomy term
 *
 * Auth: Bearer token via Authorization header.
 */
class ApiController extends BaseController
{
    public function __construct(
        private readonly BreadManager $bread,
        private readonly ApiTokenService $tokens,
        private readonly ?TaxonomyManager $taxonomy = null,
    ) {
        parent::__construct();
    }

    /**
     * List registered content types.
     */
    public function types(): Response
    {
        if (!$this->guard()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $types = [];

        foreach ($this->bread->types() as $name => $ct) {
            $types[$name] = [
                'name' => $ct->name,
                'label' => $ct->label,
                'singular' => $ct->singular,
                'icon' => $ct->icon,
                'fields' => $ct->formSchema(),
            ];
        }

        return $this->json(['data' => $types]);
    }

    /**
     * Browse records with pagination + filters.
     */
    public function index(string $type): Response
    {
        if (!$this->guard()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $contentType = $this->bread->type($type);

        if ($contentType === null) {
            return $this->json(['error' => "Unknown type: {$type}"], 404);
        }

        $page = max(1, (int) $this->request->input('page', 1));
        $perPage = min(
            (int) config('cms.api.max_per_page', 100),
            max(1, (int) $this->request->input('per_page', (int) config('cms.api.per_page', 15)))
        );
        $filters = $this->buildFilters();

        $all = $this->bread->browse($type, $filters);
        $total = count($all);
        $records = array_slice($all, ($page - 1) * $perPage, $perPage);

        return $this->json([
            'data' => $records,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
            ],
        ]);
    }

    /**
     * Read a single record.
     */
    public function show(string $type, string $id): Response
    {
        if (!$this->guard()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $record = $this->bread->read($type, $id);

        if ($record === null) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $record = $this->enrich($record, $type);

        return $this->json(['data' => $record]);
    }

    /**
     * Create a record.
     */
    public function store(string $type): Response
    {
        if (!$this->guard()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $data = $this->request->body();

        if ($data === []) {
            return $this->json(['error' => 'No data provided'], 422);
        }

        try {
            $record = $this->bread->add($type, $data);
        } catch (\Tavp\Cms\Content\ValidationException $e) {
            return $this->json(['errors' => $e->errors()], 422);
        }

        return $this->json(['data' => $record], 201);
    }

    /**
     * Update a record.
     */
    public function update(string $type, string $id): Response
    {
        if (!$this->guard()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $existing = $this->bread->read($type, $id);

        if ($existing === null) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $data = $this->request->body();

        try {
            $record = $this->bread->edit($type, $id, $data);
        } catch (\Tavp\Cms\Content\ValidationException $e) {
            return $this->json(['errors' => $e->errors()], 422);
        }

        return $this->json(['data' => $record]);
    }

    /**
     * Delete a record.
     */
    public function destroy(string $type, string $id): Response
    {
        if (!$this->guard()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $this->bread->delete($type, $id);

        return $this->json(['deleted' => true]);
    }

    /**
     * Full-text search.
     */
    public function search(): Response
    {
        if (!$this->guard()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $query = (string) $this->request->input('q', '');

        if ($query === '') {
            return $this->json(['error' => 'Missing query parameter q'], 422);
        }

        $type = $this->request->input('type');
        $limit = min(100, max(1, (int) $this->request->input('limit', 20)));

        $search = app()->getService(\Tavp\Cms\Search\SearchEngine::class);
        $results = $search->search($query, $type, $limit);

        return $this->json([
            'data' => array_map(fn (array $r) => [
                'content' => $r['content'],
                'type' => $r['type'],
                'score' => $r['score'],
            ], $results),
            'meta' => [
                'query' => $query,
                'total' => count($results),
            ],
        ]);
    }

    /**
     * List taxonomy terms.
     */
    public function taxonomyIndex(string $type): Response
    {
        if (!$this->guard()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        if ($this->taxonomy === null) {
            return $this->json(['error' => 'Taxonomy not available'], 500);
        }

        $terms = $this->taxonomy->all($type);

        return $this->json(['data' => array_map(fn ($t) => $t->toArray(), $terms)]);
    }

    /**
     * Create a taxonomy term.
     */
    public function taxonomyStore(): Response
    {
        if (!$this->guard()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        if ($this->taxonomy === null) {
            return $this->json(['error' => 'Taxonomy not available'], 500);
        }

        $data = $this->request->body();

        $term = $this->taxonomy->create($data);

        return $this->json(['data' => $term], 201);
    }

    // --- Helpers --------------------------------------------------------------

    private function guard(): bool
    {
        $token = ApiTokenService::extractToken();

        return $this->tokens->verify($token);
    }

    /**
     * @return array<string,mixed>
     */
    private function buildFilters(): array
    {
        $filters = [];

        foreach (['status', 'slug'] as $key) {
            $value = $this->request->input($key);

            if ($value !== null && $value !== '') {
                $filters[$key] = (string) $value;
            }
        }

        return $filters;
    }

    /**
     * Enrich a record with taxonomy terms if taxonomy is enabled.
     *
     * @param array<string,mixed> $record
     * @return array<string,mixed>
     */
    private function enrich(array $record, string $type): array
    {
        if ($this->taxonomy === null) {
            return $record;
        }

        $contentType = $this->bread->type($type);

        if ($contentType === null) {
            return $record;
        }

        $record['categories'] = array_map(
            fn ($t) => $t->toArray(),
            $this->taxonomy->termsFor((int) $record['id'], 'category'),
        );

        $record['tags'] = array_map(
            fn ($t) => $t->toArray(),
            $this->taxonomy->termsFor((int) $record['id'], 'tag'),
        );

        return $record;
    }
}
