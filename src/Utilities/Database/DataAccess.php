<?php

namespace MyDramLibrary\Utilities\Database;

interface DataAccess
{
    public function create(array $params);
    public function read($id);
    public function update($id, array $params): bool;
    public function delete($id): bool;
}
