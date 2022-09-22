<?php

namespace Tests;

use MyDramLibrary\Catalog\DataAccess\CatalogCategoryDataAccess;
use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Database\Database;

class CatalogCategoryDataAccessTest extends TestCase
{    
    public function testIsInstanceOfDataAccess()
    {
        $this->assertInstanceOf('MyDramLibrary\Utilities\Database\DataAccess', new CatalogCategoryDataAccess());
    }

    public function testThrowValidatorExceptionIfAddingCategoryWithoutName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $dataAccess = new CatalogCategoryDataAccess();
        $dataAccess->create(['category' => '']);
    }

    public function testThrowValidatorExceptionIfAddingCategoryWithNameLongerThan100()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $dataAccess = new CatalogCategoryDataAccess();
        $category['category'] = '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901';
        $dataAccess->create($category);
    }

    public function testReturnExistingIdWhenAddingExistingCategoryName()
    {
        $dataAccess = new CatalogCategoryDataAccess();
        $category['category'] = 'TestCategory01-' . time();
        $id1 = $dataAccess->create($category);
        $id2 = $dataAccess->create($category);
        $this->assertSame($id1, $id2);
    }

    public function testReturnIdOfCreatedCategory()
    {
        $dataAccess = new CatalogCategoryDataAccess();
        $category['category'] = 'TestCategory02-' . time();
        $categoryId = $dataAccess->create($category);

        $db = Database::instance();
        $id = $db->run('select id from category where category = \''.$category['category'] . '\'')->fetchColumn();
        $this->assertEquals($categoryId, $id);
    }

    public function testThrowExceptionIfSelectingMissingCategoryById()
    {
        $this->expectException('OutOfRangeException');
        $dataAccess = new CatalogCategoryDataAccess();
        $dataAccess->read(0);
    }

    public function testReturnCategoryNameForExistingId()
    {
        $dataAccess = new CatalogCategoryDataAccess();
        $category['category'] = 'TestCategory03-' . time();
        $categoryId = $dataAccess->create($category);

        unset($dataAccess);

        $dataAccess = new CatalogCategoryDataAccess();
        $this->assertEquals($category['category'], $dataAccess->read($categoryId)['category']);
    }

    public function testThrowExceptionWhenEditMissingCategory()
    {
        $this->expectException('OutOfRangeException');
        $dataAccess = new CatalogCategoryDataAccess();
        $dataAccess->update(0, ['category' => 'newname']);
    }

    public function testThrowValidatorExceptionIfUpdatingToWrongName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $dataAccess = new CatalogCategoryDataAccess();
        $category['category'] = 'TestCategory04-' . time();
        $categoryId = $dataAccess->create($category);

        $wrongCategory['category'] = '';
        $dataAccess->update($categoryId, $wrongCategory);
    }

    public function testReturnFalseIfUpdatingCategoryToSameName()
    {
        $dataAccess = new CatalogCategoryDataAccess();
        $category['category'] = 'TestCategory05-'.time();
        $categoryId = $dataAccess->create($category);
        $this->assertFalse($dataAccess->update($categoryId, $category));
    }

    public function testThrowPDOExceptionWhenUpdatingToSomeExistingName()
    {
        $this->expectException('PDOException');
        $dataAccess = new CatalogCategoryDataAccess();
        $category1['category'] = 'TestCategory06-' . time();
        $categoryId1 = $dataAccess->create($category1);

        $category2['category'] = 'TestCategory07-' . time();
        $dataAccess->create($category2);

        $dataAccess->update($categoryId1, $category2);        
    }

    public function testReturnTrueAfterSuccessfulUpdate()
    {
        $dataAccess = new CatalogCategoryDataAccess();
        $category['category'] = 'TestCategory08-' . time();
        $categoryId = $dataAccess->create($category);

        $category2['category'] = 'TestCategory09-' . time();
        $this->assertTrue($dataAccess->update($categoryId, $category2));
    }

    public function testThrowDomainExceptionIfCategoryKeyMissingInGivenParamToCreate()
    {
        $this->expectException('DomainException');
        $dataAccess = new CatalogCategoryDataAccess();
        $category['name'] = 'TestCategory10-' . time();
        $dataAccess->create($category);
    }

    public function testThrowInvalidArgumentExceptionWhenReadingCategoryWithNotIntegerKey()
    {
        $this->expectException('InvalidArgumentException');
        $dataAccess = new CatalogCategoryDataAccess();
        $dataAccess->read('not integer');
    }

    public function testThrowInvalidArgumentExceptionWhenUpdatingCategoryWithNotIntegerKey()
    {
        $this->expectException('InvalidArgumentException');
        $dataAccess = new CatalogCategoryDataAccess();
        $dataAccess->update('not integer', ['category' => 'catname']);
    }

    public function testThrowDomainExceptionIfCategoryKeyMissingInGivenParamToUpdate()
    {
        $this->expectException('DomainException');
        $dataAccess = new CatalogCategoryDataAccess();
        $incorrectKeyName['name'] = 'TestCategory10-' . time();
        $correctKeyName['category'] = $incorrectKeyName['name'];
        $id = $dataAccess->create($correctKeyName);
        $dataAccess->update($id, $incorrectKeyName);
    }

    public function testThrowInvalidArgumentExceptionWhenDeleteCategoryWithNotIntegerKey()
    {
        $this->expectException('InvalidArgumentException');
        $dataAccess = new CatalogCategoryDataAccess();
        $dataAccess->delete('not integer');
    }

    public function tearDown(): void
    {
        $db = Database::instance();
        $db->run('delete from category where category like \'TestCategory%\'');
    }
}
