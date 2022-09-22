<?php

namespace MyDramLibrary\Catalog;

use MyDramLibrary\Utilities\Database\Database;

class PublisherFactory
{
    protected static ?PublisherFactory $instance = null;
    protected Database $database;

    private function __construct()
    {
        $this->database = Database::instance();
    }

    public static function instance(): PublisherFactory
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getAllPublishers(): PublisherCollection
    {
        $publisherCollection = new PublisherCollection();
        $sql = 'select id from publisher';
        if ($data = $this->database->run($sql)->fetchAll(\PDO::FETCH_COLUMN, 0)) {
            foreach ($data as $publisherId) {
                $publisherId = (int) $publisherId;
                $publisherCollection->addItem(new Publisher($publisherId), $publisherId);
            }
        }
        return $publisherCollection;
    }
}
