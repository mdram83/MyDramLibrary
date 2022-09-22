<?php

namespace MyDramLibrary\Catalog;

use MyDramLibrary\Catalog\DataAccess\CatalogPublisherDataAccess;
use MyDramLibrary\CustomException\ValidatorException;
use DomainException;
use MyDramLibrary\Utilities\Validator\CatalogValidator;

class Publisher
{
    private CatalogPublisherDataAccess $dataAccess;

    private ?int $id = null;
    private ?string $name = null;

    private bool $loaded = false;
    private bool $updated = false;

    public function __construct(?int $id, string $name = null)
    {
        $this->dataAccess = new CatalogPublisherDataAccess();
        if (isset($id)) {
            $this->id = $id;
            if (isset($name)) {
                $this->load();
                $this->setName($name);
            }
        } else {
            if (!isset($name)) {
                throw new DomainException('Publisher name not specified for new Publisher');
            }
            $this->setName($name);
            $this->create();
        }
    }

    public function __destruct()
    {
        $this->update();
    }

    public function getId(): int
    {
        $this->load();
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->validatePublisherName($name);
        $this->name = $name;
        $this->updated = true;
    }

    public function getName(): string
    {
        $this->load();
        return $this->name;
    }

    private function load(): void
    {
        if (!$this->loaded) {
            $this->name = $this->dataAccess->read($this->id)['publisher'];
            $this->loaded = true;
        }
    }

    private function update(): void
    {
        if ($this->updated) {
            $this->dataAccess->update($this->id, ['publisher' => $this->name]);
            $this->updated = false;
        }
    }

    private function create(): void
    {
        $this->id = $this->dataAccess->create(['publisher' => $this->name]);
        $this->loaded = true;
        $this->updated = false;
    }

    private function validatePublisherName(string $name): void
    {
        if (!CatalogValidator::isValidPublisherName($name)) {
            throw new ValidatorException('Invalid publisher name');
        }
    }
}
