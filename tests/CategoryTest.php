<?php

namespace Tests;

use MyDramLibrary\Catalog\Category;
use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Database\Database;

class CategoryTest extends TestCase
{
    private Database $db;
    private $categoryId;
    private string $categoryName;

    private string $validCategoryName;
    private string $validCategoryName2;

    final public function setUp(): void
    {
        $this->db = Database::instance();
        $this->categoryName = 'CategoryClass-Test-01-'.time();
        $sql = 'insert into category (category) values (:category)';
        $this->db->run($sql, ['category' => $this->categoryName]);
        $this->categoryId = $this->db->lastInsertId();

        $this->validCategoryName = 'CategoryClass-Test-'.time();
        $this->validCategoryName2 = $this->validCategoryName.'updated';
    }

    public function testCategoryCreatedWithId()
    {
        $this->assertInstanceOf('MyDramLibrary\Catalog\Category', new Category($this->categoryId));
    }

    public function testCreateCategoryObjectWithValidName()
    {
        $this->assertInstanceOf('MyDramLibrary\Catalog\Category', new Category(null, $this->validCategoryName));
    }

    public function testThrowValidatorExceptionWhenSettingCategoryToNotAllowedName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $category = new Category(null, $this->validCategoryName);
        $invalidName = '%%%<><><><><>>>><<asds ><';
        $category->setName($invalidName);
    }

    public function testGetProperNameAfterItWasSet()
    {
        $category = new Category(null, $this->validCategoryName);
        $this->assertEquals($this->validCategoryName, $category->getName());
    }

    public function testThrowExceptionIfCreatingNewCategoryWithoutIdAndName()
    {
        $this->expectException('DomainException');
        new Category(null);
    }

    public function testReturnNameForAlreadyExistingCategory()
    {
        $category = new Category($this->categoryId);
        $this->assertEquals($this->categoryName, $category->getName());
    }

    public function testUpdatedNameIsSavedAndReturned()
    {
        $category = new Category($this->categoryId);
        $category->setName($this->validCategoryName);
        unset ($category);

        $category2 = new Category($this->categoryId);
        $this->assertEquals($this->validCategoryName, $category2->getName());
    }

    public function testGetIdOfExistingCategory()
    {
        $category = new Category($this->categoryId);
        $this->assertEquals($this->categoryId, $category->getId());
    }

    public function testReturnIdOfNewlyCreatedCategory()
    {
        $category = new Category(null, $this->validCategoryName);
        $sql = 'select id from category where category = :category';
        $this->assertEquals($this->db->run($sql, ['category' => $this->validCategoryName])->fetchColumn(), $category->getId());
    }

    public function testGetExistingIdIfSavingDuplicate()
    {
        $category = new Category(null, $this->categoryName);
        $this->assertEquals($this->categoryId, $category->getId());
    }

    public function testNewCategoryImmediatelyUpdatedIsSavedProperly()
    {
        $name = $this->validCategoryName;
        $category = new Category(null, $name);
        $id = $category->getId();

        $category->setName($this->validCategoryName2);
        unset($category);

        $category2 = new Category($id);
        $this->assertEquals($this->validCategoryName2, $category2->getName());
    }

    public function testNameIsUpdatedForExistingCategoryWhenPassedInConstructor()
    {
        $category = new Category($this->categoryId, $this->validCategoryName);
        unset($category);
        $categoryUpdated = new Category($this->categoryId);
        $this->assertEquals($this->validCategoryName, $categoryUpdated->getName());
    }

    public function testThrowOutOfRangeExceptionWhenAskingFOrCategoryWithIncorrectId()
    {
        $this->expectException('OutOfRangeException');
        $category = new Category(-1);
        $category->getName();
    }

    public function tesCategoryNewNameIsNotOverwrittenAccidentallyByLoadingDBParams()
    {
        $category = new Category($this->categoryId, $this->validCategoryName);
        $category->getId();
        $this->assertTrue($this->validCategoryName == $category->getName());
    }

    public function tearDown(): void
    {
        $sql = 'delete from category where id = :id or category = :category or category = :updatedcat';
        $this->db->run($sql, [
            'id' => $this->categoryId,
            'category' => $this->validCategoryName,
            'updatedcat' => $this->validCategoryName2
        ]);
        unset($this->categoryId);
        unset($this->categoryName);
    }
}
