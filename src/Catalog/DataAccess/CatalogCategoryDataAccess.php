<?php

namespace MyDramLibrary\Catalog\DataAccess;

use MyDramLibrary\CustomException\ValidatorException;
use DomainException;
use OutOfRangeException;
use MyDramLibrary\Utilities\Validator\CatalogValidator;

class CatalogCategoryDataAccess extends CatalogDataAccess
{
    protected function doCreate(array $params): int
    {
        $category = $this->validateCategoryName($params['category']);

        if ($id = $this->getCategoryIdByName($category)) {
            return $id;
        }

        $sql = 'insert into category (category) values (:category)';
        $this->dataHandle->run($sql, ['category' => $category]);
        return $this->dataHandle->lastInsertId();
    }

    protected function doRead(int $id): array
    {
        $sql = 'select category from category where id = :id';
        if ($params['category'] = $this->dataHandle->run($sql, ['id' => $id])->fetchColumn()) {
            return $params;
        } else {
            throw new OutOfRangeException('Incorrect category id');
        }
    }

    protected function doUpdate(int $id, array $params): bool
    {
        $category = $this->validateCategoryName($params['category']);

        if ($category == $this->read($id)['category']) {
            return false;
        }

        $sql = 'update category set category = :category where id = :id';
        $this->dataHandle->run($sql, ['category' => $category, 'id' => $id]);
        return true;
    }

    protected function doDelete(int $id): bool
    {
        return false;
    }

    protected function validateParams(array $params): void
    {
        if (!isset($params['category'])) {
            throw new DomainException('Category key not set in parameters array');
        }
    }

    private function validateCategoryName(string $category): string
    {
        if (!CatalogValidator::isValidCategoryName($category)) {
            throw new ValidatorException('Invalid category name');
        }
        return $category;
    }

    private function getCategoryIdByName(string $category): int
    {
        $sql = 'select id from category where category = :category';
        if ($id = $this->dataHandle->run($sql, ['category' => $category])->fetchColumn()) {
            return $id;
        }
        return 0;
    }
}
