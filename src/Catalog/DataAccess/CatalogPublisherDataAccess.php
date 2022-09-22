<?php

namespace MyDramLibrary\Catalog\DataAccess;

use MyDramLibrary\CustomException\ValidatorException;
use DomainException;
use OutOfRangeException;
use MyDramLibrary\Utilities\Validator\CatalogValidator;

class CatalogPublisherDataAccess extends CatalogDataAccess
{
    protected function doCreate(array $params): int
    {
        $publisher = $this->validatePublisherName($params['publisher']);

        if ($id = $this->getPublisherIdByName($publisher)) {
            return $id;
        }

        $sql = 'insert into publisher (publisher) values (:publisher)';
        $this->dataHandle->run($sql, ['publisher' => $publisher]);
        return $this->dataHandle->lastInsertId();
    }

    protected function doRead(int $id): array
    {
        $sql = 'select publisher from publisher where id = :id';
        if ($params['publisher'] = $this->dataHandle->run($sql, ['id' => $id])->fetchColumn()) {
            return $params;
        } else {
            throw new OutOfRangeException('Incorrect publisher id');
        }
    }

    protected function doUpdate(int $id, array $params): bool
    {
        $publisher = $this->validatePublisherName($params['publisher']);

        if ($publisher == $this->read($id)['publisher']) {
            return false;
        }

        $sql = 'update publisher set publisher = :publisher where id = :id';
        $this->dataHandle->run($sql, ['publisher' => $publisher, 'id' => $id]);
        return true;
    }

    protected function doDelete(int $id): bool
    {
        return false;
    }

    protected function validateParams(array $params): void
    {
        if (!isset($params['publisher'])) {
            throw new DomainException('Publisher key not set in parameters array');
        }
    }

    private function validatePublisherName(string $publisher): string
    {
        if (!CatalogValidator::isValidPublisherName($publisher)) {
            throw new ValidatorException('Invalid publisher name');
        }
        return $publisher;
    }

    private function getPublisherIdByName(string $publisher): int
    {
        $sql = 'select id from publisher where publisher = :publisher';
        if ($id = $this->dataHandle->run($sql, ['publisher' => $publisher])->fetchColumn()) {
            return $id;
        }
        return 0;
    }
}
