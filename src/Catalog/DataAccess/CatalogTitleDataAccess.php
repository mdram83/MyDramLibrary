<?php

namespace MyDramLibrary\Catalog\DataAccess;

use DomainException;
use OutOfRangeException;

class CatalogTitleDataAccess extends CatalogDataAccess
{
    protected function doCreate(array $params): int
    {
        $sql = '
            insert into title (
                user,
                title,
                author,
                publisher,
                isbn,
                series,
                volume,
                pages,
                description,
                comment
            ) values (
                :user,
                :title,
                :author,
                :publisher,
                :isbn,
                :series,
                :volume,
                :pages,
                :description,
                :comment
            )';

        $categories = $this->extractCategoriesFromParams($params);

        $this->dataHandle->run($sql, $params);
        $id = $this->dataHandle->lastInsertId();

        $this->updateTitleCategories($id, $categories);

        return $id;
    }

    protected function doRead(int $id): array
    {
        $sql = '
            select
                user,
                title,
                author,
                publisher,
                isbn,
                series,
                volume,
                pages,
                description,
                comment
            from title
            where id = :id';
        if ($params = $this->dataHandle->run($sql, ['id' => $id])->fetch()) {
            $this->convertSQLStringToInt($params, array('user', 'author', 'publisher'));
            return array_merge($params, ['categories' => $this->readTitleCategoryRelations($id)]);
        } else {
            throw new OutOfRangeException('Incorrect title id');
        }
    }

    protected function doUpdate(int $id, array $params): bool
    {
        if ($params == $this->read($id)) {
            return false;
        }
        $sql = '
            update title set
                user = :user,
                title = :title,
                author = :author,
                publisher = :publisher,
                isbn = :isbn,
                series = :series,
                volume = :volume,
                pages = :pages,
                description = :description,
                comment = :comment
            where id = :id';

        $categories = $this->extractCategoriesFromParams($params);
        $this->updateTitleCategories($id, $categories);
        $this->dataHandle->run($sql, array_merge($params, ['id' => $id]));
        return true;
    }

    protected function doDelete(int $id): bool
    {
        $this->doRead($id);
        $sql = 'delete from title where id = :id';
        $this->dataHandle->run($sql, ['id' => $id]);
        return true;
    }

    protected function validateParams(array $params): void
    {
        if (
            !(
            isset($params['user']) &&
            isset($params['title']) &&
            array_key_exists('author', $params) &&
            array_key_exists('publisher', $params) &&
            array_key_exists('isbn', $params) &&
            array_key_exists('series', $params) &&
            array_key_exists('volume', $params) &&
            array_key_exists('pages', $params) &&
            array_key_exists('description', $params) &&
            array_key_exists('comment', $params) &&
            array_key_exists('categories', $params)
            )
        ) {
            throw new DomainException('Title keys not correctly set in parameters array');
        }

        if (!is_array($params['categories'])) {
            throw new DomainException('Title categories must be an array of categories');
        }

        foreach ($params['categories'] as $category) {
            if (!is_int($category)) {
                throw new DomainException('Title categories array must contain category keys');
            }
        }
    }

    private function extractCategoriesFromParams(array &$params): array
    {
        $categories = $params['categories'];
        unset($params['categories']);
        return $categories;
    }

    private function updateTitleCategories(int $titleId, array $targetCategories): void
    {
        $currentCategories = $this->readTitleCategoryRelations($titleId);
        $categoriesToAdd = array_diff($targetCategories, $currentCategories);
        $categoriesToRemove = array_diff($currentCategories, $targetCategories);

        foreach ($categoriesToAdd as $categoryId) {
            $this->addTitleCategoryRelation($titleId, $categoryId);
        }

        foreach ($categoriesToRemove as $categoryId) {
            $this->deleteTitleCategoryRelation($titleId, $categoryId);
        }
    }

    private function readTitleCategoryRelations(int $titleId): array
    {
        $sql = 'select category from title_category where title = :title';
        if ($categories = $this->dataHandle->run($sql, ['title' => $titleId])->fetchAll(\PDO::FETCH_COLUMN, 0)) {
            $this->convertSQLStringToInt($categories, array_keys($categories));
            return $categories;
        }
        return array();
    }

    private function deleteTitleCategoryRelation(int $titleId, int $categoryId): void
    {
        $sql = 'delete from title_category where title = :title and category = :category';
        $this->dataHandle->run($sql, ['title' => $titleId, 'category' => $categoryId]);
    }

    private function addTitleCategoryRelation(int $titleId, int $categoryId): void
    {
        $sql = 'insert into title_category (title, category) values (:title, :category)';
        $this->dataHandle->run($sql, ['title' => $titleId, 'category' => $categoryId]);
    }
}
