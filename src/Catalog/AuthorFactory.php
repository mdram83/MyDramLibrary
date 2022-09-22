<?php

namespace MyDramLibrary\Catalog;

use MyDramLibrary\Utilities\Database\Database;

class AuthorFactory
{
    protected static ?AuthorFactory $instance = null;
    protected Database $database;

    private function __construct()
    {
        $this->database = Database::instance();
    }

    public static function instance(): AuthorFactory
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getAllAuthors(): AuthorCollection
    {
        $authorCollection = new AuthorCollection();
        $sql = 'select id from author';
        if ($data = $this->database->run($sql)->fetchAll(\PDO::FETCH_COLUMN, 0)) {
            foreach ($data as $authorId) {
                $authorId = (int) $authorId;
                $authorCollection->addItem(new Author($authorId), $authorId);
            }
        }
        return $authorCollection;
    }
}
