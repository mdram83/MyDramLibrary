<?php

namespace MyDramLibrary\Catalog;

use MyDramLibrary\Catalog\DataAccess\CatalogTitleDataAccess;
use MyDramLibrary\CustomException\ValidatorException;
use DomainException;
use Exception;
use MyDramLibrary\User\User;
use MyDramLibrary\Utilities\Validator\CatalogValidator;

class Title
{
    private CatalogTitleDataAccess $dataAccess;

    private ?int $id = null;
    private ?User $user = null;
    private ?string $title = null;
    private ?Author $author = null;
    private ?Publisher $publisher = null;
    private ?string $isbn = null;
    private ?string $series = null;
    private ?int $volume = null;
    private ?int $pages = null;
    private ?string $description = null;
    private ?string $comment = null;
    private CategoryCollection $categories;

    private bool $loaded = false;
    private bool $updated = false;
    private bool $delete = false;

    public function __construct(
        ?int $id,
        User $user = null,
        string $title = null,
        Author $author = null,
        Publisher $publisher = null,
        string $isbn = null,
        string $series = null,
        int $volume = null,
        int $pages = null,
        string $description = null,
        string $comment = null,
        CategoryCollection $categories = null,
    ) {
        $this->dataAccess = new CatalogTitleDataAccess();
        $this->categories = new CategoryCollection();

        if (isset($user)) {
            try {
                $user->getUserId();
            } catch (Exception $e) {
                throw new DomainException('Existing user required to create title');
            }
        }

        if (isset($id)) {
            $this->id = $id;
            if (
                isset($user) ||
                isset($title) ||
                isset($author) ||
                isset($publisher) ||
                isset($isbn) ||
                isset($series) ||
                isset($volume) ||
                isset($pages) ||
                isset($description) ||
                isset($comment) ||
                isset($categories)
            ) {
                $this->load();

                if (isset($user) && $user->getUserId() != $this->user->getUserId()) {
                    throw new DomainException('Can not change user for Title');
                }

                if (isset($title)) {
                    $this->setTitle($title);
                }
                if (isset($author)) {
                    $this->setAuthor($author);
                }
                if (isset($publisher)) {
                    $this->setPublisher($publisher);
                }
                if (isset($isbn)) {
                    $this->setISBN($isbn);
                }
                if (isset($series)) {
                    $this->setSeries($series);
                }
                if (isset($volume)) {
                    $this->setVolume($volume);
                }
                if (isset($pages)) {
                    $this->setPages($pages);
                }
                if (isset($description)) {
                    $this->setDescription($description);
                }
                if (isset($comment)) {
                    $this->setComment($comment);
                }
                if (isset($categories)) {
                    $this->setCategories($categories);
                }
            }
        } else {
            if (!isset($user) || !isset($title)) {
                throw new DomainException('Mandatory parameters not specified for new Title');
            }

            $this->user = $user;
            $this->setTitle($title);

            if (isset($author)) {
                $this->setAuthor($author);
            }
            if (isset($publisher)) {
                $this->setPublisher($publisher);
            }
            if (isset($isbn)) {
                $this->setISBN($isbn);
            }
            if (isset($series)) {
                $this->setSeries($series);
            }
            if (isset($volume)) {
                $this->setVolume($volume);
            }
            if (isset($pages)) {
                $this->setPages($pages);
            }
            if (isset($description)) {
                $this->setDescription($description);
            }
            if (isset($comment)) {
                $this->setComment($comment);
            }
            if (isset($categories)) {
                $this->setCategories($categories);
            }
            $this->create();
        }
    }

    public function __destruct()
    {
        $this->update();
        $this->delete();
    }

    public function deleteTitle(): void
    {
        $this->delete = true;
    }

    public function getId(): int
    {
        $this->load();
        return $this->id;
    }

    public function getUser(): User
    {
        $this->load();
        return $this->user;
    }

    public function setTitle(string $title): void
    {
        $this->preload();
        $this->validateTitle($title);
        $this->title = $title;
        $this->updated = true;
    }

    public function getTitle(): string
    {
        $this->load();
        return $this->title;
    }

    public function setAuthor(?Author $author): void
    {
        $this->preload();
        $this->author = $author;
        $this->updated = true;
    }

    public function getAuthor(): ?Author
    {
        $this->load();
        return $this->author;
    }

    public function setPublisher(?Publisher $publisher): void
    {
        $this->preload();
        $this->publisher = $publisher;
        $this->updated = true;
    }

    public function getPublisher(): ?Publisher
    {
        $this->load();
        return $this->publisher;
    }

    public function setISBN(?string $isbn): void
    {
        $this->preload();
        if (isset($isbn)) {
            $this->validateISBN($isbn);
        }
        $this->isbn = $isbn;
        $this->updated = true;
    }

    public function getISBN(): ?string
    {
        $this->load();
        return $this->isbn;
    }

    public function setSeries(?string $series): void
    {
        $this->preload();
        if (isset($series)) {
            $this->validateSeries($series);
        }
        $this->series = $series;
        $this->updated = true;
    }

    public function getSeries(): ?string
    {
        $this->load();
        return $this->series;
    }

    public function setVolume(?int $volume): void
    {
        $this->preload();
        if (isset($volume)) {
            $this->validateVolume($volume);
        }
        $this->volume = $volume;
        $this->updated = true;
    }

    public function getVolume(): ?int
    {
        $this->load();
        return $this->volume;
    }

    public function setPages(?int $pages): void
    {
        $this->preload();
        if (isset($pages)) {
            $this->validatePages($pages);
        }
        $this->pages = $pages;
        $this->updated = true;
    }

    public function getPages(): ?int
    {
        $this->load();
        return $this->pages;
    }

    public function setDescription(?string $description): void
    {
        $this->preload();
        if (isset($description)) {
            $this->validateDescription($description);
        }
        $this->description = $description;
        $this->updated = true;
    }

    public function getDescription(): ?string
    {
        $this->load();
        return $this->description;
    }

    public function setComment(?string $comment): void
    {
        $this->preload();
        if (isset($comment)) {
            $this->validateComment($comment);
        }
        $this->comment = $comment;
        $this->updated = true;
    }

    public function getComment(): ?string
    {
        $this->load();
        return $this->comment;
    }

    public function setCategories(CategoryCollection $categories): void
    {
        $this->preload();
        $this->categories = $categories;
        $this->updated = true;
    }

    public function getCategories(): CategoryCollection
    {
        $this->load();
        return $this->categories;
    }

    public function getDataArray(): array
    {
        foreach ($this->getCategories() as $category) {
            $categories[] = $category->getName();
        }
        return [
            'id'              => $this->getId(),
            'title'           => $this->getTitle(),
            'authorFirstname' => ($this->getAuthor() === null) ? null : $this->getAuthor()->getFirstname(),
            'authorLastname'  => ($this->getAuthor() === null) ? null : $this->getAuthor()->getLastname(),
            'author'          => ($this->getAuthor() === null) ? null : $this->getAuthor()->getAuthorName(),
            'publisher'       => ($this->getPublisher() === null) ? null : $this->getPublisher()->getName(),
            'isbn'            => $this->getISBN(),
            'series'          => $this->getSeries(),
            'volume'          => $this->getVolume(),
            'pages'           => $this->getPages(),
            'description'     => $this->getDescription(),
            'comment'         => $this->getComment(),
            'category'        => $categories ?? null,
        ];
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

            $this->user = new User($data['user']); // factory later?
            $this->title = $data['title'];
            $this->author = ($data['author'] != null) ? new Author($data['author']) : null; // factory later?
            $this->publisher = ($data['publisher'] != null) ? new Publisher($data['publisher']) : null; // factory?
            $this->isbn = $data['isbn'];
            $this->series = $data['series'];
            $this->volume = $data['volume'];
            $this->pages = $data['pages'];
            $this->description = $data['description'];
            $this->comment = $data['comment'];
            foreach ($data['categories'] as $categoryId) {
                $this->categories->addItem(new Category($categoryId), $categoryId); // factory later?
            }

            $this->loaded = true;
        }
    }

    private function update(): void
    {
        if ($this->updated) {
            $titleParams = [
                'user' => $this->user->getUserId(),
                'title' => $this->title,
                'author' => ($this->author != null) ? $this->author->getId() : null,
                'publisher' => ($this->publisher != null) ? $this->publisher->getId() : null,
                'isbn' => $this->isbn,
                'series' => $this->series,
                'volume' => $this->volume,
                'pages' => $this->pages,
                'description' => $this->description,
                'comment' => $this->comment,
                'categories' => array(),
            ];
            foreach ($this->categories as $category) {
                $titleParams['categories'][] = $category->getId();
            }
            $this->dataAccess->update($this->id, $titleParams);
            $this->updated = false;
        }
    }

    private function delete(): void
    {
        if ($this->delete) {
            $this->dataAccess->delete($this->id);
        }
    }

    private function create(): void
    {
        $titleParams = [
            'user' => $this->user->getUserId(),
            'title' => $this->title,
            'author' => ($this->author != null) ? $this->author->getId() : null,
            'publisher' => ($this->publisher != null) ? $this->publisher->getId() : null,
            'isbn' => $this->isbn,
            'series' => $this->series,
            'volume' => $this->volume,
            'pages' => $this->pages,
            'description' => $this->description,
            'comment' => $this->comment,
            'categories' => array(),
        ];
        foreach ($this->categories as $category) {
            $titleParams['categories'][] = $category->getId();
        }
        $this->id = $this->dataAccess->create($titleParams);
        $this->loaded = true;
        $this->updated = false;
    }

    private function validateTitle(string $title): void
    {
        if (!CatalogValidator::isValidTitle($title)) {
            throw new ValidatorException('Invalid input in title');
        }
    }

    private function validateISBN(string $isbn): void
    {
        if (!CatalogValidator::isValidISBN($isbn)) {
            throw new ValidatorException('Invalid ISBN');
        }
    }

    private function validateSeries(string $series): void
    {
        if (!CatalogValidator::isValidSeries($series)) {
            throw new ValidatorException('Invalid Series');
        }
    }

    private function validateVolume(int $volume): void
    {
        if (!CatalogValidator::isValidVolume($volume)) {
            throw new ValidatorException('Invalid Volume');
        }
    }

    private function validatePages(int $pages): void
    {
        if (!CatalogValidator::isValidPages($pages)) {
            throw new ValidatorException('Invalid Pages');
        }
    }

    private function validateDescription(string $description): void
    {
        if (!CatalogValidator::isValidDescription($description)) {
            throw new ValidatorException('Invalid Description');
        }
    }

    private function validateComment(string $comment): void
    {
        if (!CatalogValidator::isValidComment($comment)) {
            throw new ValidatorException('Invalid Comment');
        }
    }
}
