<?php

namespace MyDramLibrary\Catalog\DataAccess;

use InvalidArgumentException;
use MyDramLibrary\Utilities\Database\DataAccess;
use MyDramLibrary\Utilities\Database\Database;

abstract class CatalogDataAccess implements DataAccess
{
    protected Database $dataHandle;

    public function __construct()
    {
        $this->openDataHandle();
    }

    final protected function openDataHandle(): void
    {
        $this->dataHandle = Database::instance();
    }

    final protected function closeDataHandle(): void
    {
        unset($this->dataHandle);
    }

    public function create(array $params): int
    {
        $this->validateParams($params);
        return $this->doCreate($params);
    }

    public function read($id): array
    {
        $this->validateRecordId($id);
        return $this->doRead($id);
    }

    public function update($id, array $params): bool
    {
        $this->validateParams($params);
        $this->validateRecordId($id);
        return $this->doUpdate($id, $params);
    }

    public function delete($id): bool
    {
        $this->validateRecordId($id);
        return $this->doDelete($id);
    }

    abstract protected function doCreate(array $params): int;
    abstract protected function doRead(int $id): array;
    abstract protected function doUpdate(int $id, array $params): bool;
    abstract protected function doDelete(int $id): bool;

    abstract protected function validateParams(array $params): void;

    protected function validateRecordId($id): void
    {
        if (!is_int($id)) {
            throw new InvalidArgumentException('Record key should be an integer');
        }
    }

    protected function convertSQLStringToInt(array &$params, array $keys): void
    {
        foreach ($keys as $key) {
            $params[$key] = (int) $params[$key];
        }
    }
}
