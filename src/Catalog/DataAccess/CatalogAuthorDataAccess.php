<?php

namespace MyDramLibrary\Catalog\DataAccess;

use MyDramLibrary\CustomException\ValidatorException;
use DomainException;
use OutOfRangeException;
use MyDramLibrary\Utilities\Validator\CatalogValidator;

class CatalogAuthorDataAccess extends CatalogDataAccess
{
    protected function doCreate(array $params): int
    {
        $firstname = $this->validateAuthorName($params['firstname']);
        $lastname = $this->validateAuthorName($params['lastname']);

        if ($id = $this->getAuthorIdByName($firstname, $lastname)) {
            return $id;
        }

        $sql = 'insert into author (firstName, lastName) values (:firstname, :lastname)';
        $this->dataHandle->run($sql, ['firstname' => $firstname, 'lastname' => $lastname]);
        return $this->dataHandle->lastInsertId();
    }

    protected function doRead(int $id): array
    {
        $sql = 'select firstName as firstname, lastName as lastname from author where id = :id';
        if ($params = $this->dataHandle->run($sql, ['id' => $id])->fetch()) {
            return $params;
        } else {
            throw new OutOfRangeException('Incorrect author id');
        }
    }

    protected function doUpdate(int $id, array $params): bool
    {
        $firstname = $this->validateAuthorName($params['firstname']);
        $lastname = $this->validateAuthorName($params['lastname']);

        $data = $this->read($id);
        if ($firstname == $data['firstname'] && $lastname == $data['lastname']) {
            return false;
        }

        $sql = 'update author set firstName = :firstname, lastName = :lastname where id = :id';
        $this->dataHandle->run($sql, ['firstname' => $firstname, 'lastname' => $lastname, 'id' => $id]);
        return true;
    }

    protected function doDelete(int $id): bool
    {
        return false;
    }

    protected function validateParams(array $params): void
    {
        if (!(isset($params['firstname']) && isset($params['lastname']))) {
            throw new DomainException('Author key not set in parameters array');
        }
    }

    private function validateAuthorName(string $name): string
    {
        if (!CatalogValidator::isValidAuthorName($name)) {
            throw new ValidatorException('Invalid author name');
        }
        return $name;
    }

    private function getAuthorIdByName(string $firstname, string $lastname): int
    {
        $sql = 'select id from author where firstName = :firstname and lastName = :lastname';
        if ($id = $this->dataHandle->run($sql, ['firstname' => $firstname, 'lastname' => $lastname])->fetchColumn()) {
            return $id;
        }
        return 0;
    }
}
