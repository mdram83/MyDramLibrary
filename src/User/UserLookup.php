<?php

namespace MyDramLibrary\User;

use Exception;
use MyDramLibrary\Utilities\Database\Database;

class UserLookup
{
    private string $lookupValue;
    private ?int $userId;

    private Database $database;
    private bool $loaded = false;

    public function setLookupValue(string $lookupValue): void
    {
        $this->lookupValue = $lookupValue;
        $this->userId = null;
        $this->loaded = false;
    }

    public function getLookupValue(): string
    {
        if (!isset($this->lookupValue)) {
            throw new Exception('Looukp value not set');
        } else {
            return $this->lookupValue;
        }
    }

    public function getUserId(): int
    {
        $this->load();
        return $this->userId;
    }

    private function load(): void
    {
        if (!$this->loaded) {
            $this->database = Database::instance();
            $sql = 'select id from user where username = :lookup or email = :lookup';
            $this->userId = $this->database->run($sql, ['lookup' => $this->getLookupValue()])->fetchColumn();
            $this->loaded = true;
        }
    }
}
