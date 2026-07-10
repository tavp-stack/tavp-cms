<?php

declare(strict_types=1);

namespace Tavp\Cms\Menu;

/**
 * Menu builder — turns a flat list of menu items into a nested tree ready
 * for rendering in a theme.
 */
class MenuBuilder
{
    /**
     * Build a nested tree from flat menu_items rows.
     *
     * @param array<int,array<string,mixed>> $items rows with id, parent_id, sort
     * @return array<int,array<string,mixed>> nested tree (each node gets "children")
     */
    public function tree(array $items): array
    {
        $byParent = [];

        foreach ($items as $item) {
            $parent = $item['parent_id'] ?? 0;
            $byParent[$parent ?: 0][] = $item;
        }

        foreach ($byParent as &$group) {
            usort($group, fn ($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));
        }
        unset($group);

        return $this->build($byParent, 0);
    }

    /**
     * @param array<int|string,array<int,array<string,mixed>>> $byParent
     * @return array<int,array<string,mixed>>
     */
    private function build(array $byParent, int|string $parentId): array
    {
        $branch = [];

        foreach ($byParent[$parentId] ?? [] as $item) {
            $item['children'] = $this->build($byParent, $item['id']);
            $branch[] = $item;
        }

        return $branch;
    }
}
