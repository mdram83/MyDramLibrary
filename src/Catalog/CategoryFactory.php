<?php

namespace MyDramLibrary\Catalog;

use MyDramLibrary\Utilities\Database\Database;

class CategoryFactory
{
    protected static ?CategoryFactory $instance = null;
    protected Database $database;

    private function __construct()
    {
        $this->database = Database::instance();
    }

    public static function instance(): CategoryFactory
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createCategoryCollectionFromNamesArray(array $names): CategoryCollection
    {
        $categoryCollection = new CategoryCollection();
        foreach ($names as $categoryName) {
            $categoryName = trim($categoryName);
            if ($categoryName != '') {
                $categoryCollection->addItem(new Category(null, $categoryName));
            }
        }
        return $categoryCollection;
    }

    public function getAllCategories(): CategoryCollection
    {
        $categoryCollection = new CategoryCollection();
        $sql = 'select id from category';
        if ($data = $this->database->run($sql)->fetchAll(\PDO::FETCH_COLUMN, 0)) {
            foreach ($data as $categoryId) {
                $categoryId = (int) $categoryId;
                $categoryCollection->addItem(new Category($categoryId), $categoryId);
            }
        }
        return $categoryCollection;
    }
}
