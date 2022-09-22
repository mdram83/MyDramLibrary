<?php

namespace Tests;

use MyDramLibrary\Catalog\Category;
use MyDramLibrary\Catalog\CategoryCollection;
use PHPUnit\Framework\TestCase;
use stdClass;
use MyDramLibrary\Utilities\Database\Database;

class CategoryCollectionTest extends TestCase
{
    final public function setUp(): void
    {
    }

    final public function tearDown(): void
    {
        $db = Database::instance();
        $db->run('delete from category where category = \'TestCategoryForCategoryCollection\'');
    }

    public function testCollectionCreated()
    {
        $this->assertInstanceOf('MyDramLibrary\Catalog\CategoryCollection', new CategoryCollection());
    }

    public function testThrowInvalidArgumentExceptionIfAddingNonCatalogObjectToCollection()
    {
        $this->expectException('InvalidArgumentException');
        $collection = new CategoryCollection();
        $collection->addItem(new stdClass());
    }

    public function testAcceptCatalogObjectInAddingItem()
    {
        $collection = new CategoryCollection();
        $collection->addItem(new Category(null, 'TestCategoryForCategoryCollection'));
        $this->addToAssertionCount(1);
    }
}
