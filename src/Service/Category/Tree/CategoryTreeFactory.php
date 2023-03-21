<?php
declare(strict_types=1);

namespace App\Service\Category\Tree;

use App\Entity\Category\Category;

class CategoryTreeFactory
{
    /**
     * @param Category[] $categories
     * @return Category[]
     */
    public function create(array $categories): array
    {
        $ids = array_map(static function (Category $category) {
            return $category->getId();
        }, $categories);

        $tree = array_filter($categories, static function (Category $category) use ($ids) {
            // на первом уровне категории без родителя
            return !in_array($category->getParentId(), $ids, true);
        });

        foreach ($tree as $category) {
            $this->recursiveTree($category, $categories);
        }

        return $tree;
    }

    /**
     * @param Category[] $search
     */
    private function recursiveTree(Category $category, array $search): void
    {
        foreach ($search as $subCategory) {
            if ($subCategory->getParentId() === $category->getId()) {
                $category->addChildCategory($subCategory);
                $this->recursiveTree($subCategory, $search);
            }
        }
    }
}
