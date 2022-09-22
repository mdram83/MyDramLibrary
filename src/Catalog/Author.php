<?php

namespace MyDramLibrary\Catalog;

use MyDramLibrary\Catalog\DataAccess\CatalogAuthorDataAccess;
use MyDramLibrary\CustomException\ValidatorException;
use DomainException;
use MyDramLibrary\Utilities\Validator\CatalogValidator;

class Author
{
    private CatalogAuthorDataAccess $dataAccess;

    private ?int $id = null;
    private ?string $firstname = null;
    private ?string $lastname = null;

    private bool $loaded = false;
    private bool $updated = false;

    public function __construct(?int $id, string $firstname = null, string $lastname = null)
    {
        $this->dataAccess = new CatalogAuthorDataAccess();
        if (isset($id)) {
            $this->id = $id;
            if (isset($firstname) || isset($lastname)) {
                $this->load();
                if (isset($firstname)) {
                    $this->setFirstname($firstname);
                }
                if (isset($lastname)) {
                    $this->setLastname($lastname);
                }
            }
        } else {
            if (!isset($firstname) || !isset($lastname)) {
                throw new DomainException('Author names not specified for new Author');
            }
            $this->setFirstname($firstname);
            $this->setLastname($lastname);
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

    public function setFirstname(string $name): void
    {
        $this->preload();
        $this->validateAuthorName($name);
        $this->firstname = $name;
        $this->updated = true;
    }

    public function setLastname(string $name): void
    {
        $this->preload();
        $this->validateAuthorName($name);
        $this->lastname = $name;
        $this->updated = true;
    }

    public function getFirstname(): string
    {
        $this->load();
        return $this->firstname;
    }

    public function getLastname(): string
    {
        $this->load();
        return $this->lastname;
    }

    public function getAuthorName(): string
    {
        $this->load();
        return $this->getLastName() . ', ' . $this->getFirstName();
    }

    private function preload(): void
    {
        if (isset($this->id)) {
            $this->load();
        }
    }

    private function load(): void
    {
        if (!$this->loaded) {
            $data = $this->dataAccess->read($this->id);
            $this->firstname = $data['firstname'];
            $this->lastname = $data['lastname'];
            $this->loaded = true;
        }
    }

    private function update(): void
    {
        if ($this->updated) {
            $this->dataAccess->update($this->id, ['firstname' => $this->firstname, 'lastname' => $this->lastname]);
            $this->updated = false;
        }
    }

    private function create(): void
    {
        $this->id = $this->dataAccess->create(['firstname' => $this->firstname, 'lastname' => $this->lastname]);
        $this->loaded = true;
        $this->updated = false;
    }

    private function validateAuthorName(string $name): void
    {
        if (!CatalogValidator::isValidAuthorName($name)) {
            throw new ValidatorException('Invalid author name');
        }
    }
}
