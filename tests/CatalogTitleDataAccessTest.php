<?php

namespace Tests;

use MyDramLibrary\Catalog\Author;
use MyDramLibrary\Catalog\Category;
use MyDramLibrary\Catalog\DataAccess\CatalogTitleDataAccess;
use MyDramLibrary\Catalog\Publisher;
use PHPUnit\Framework\TestCase;
use MyDramLibrary\User\User;
use MyDramLibrary\Utilities\Database\DataAccess;
use MyDramLibrary\Utilities\Database\Database;

class CatalogTitleDataAccessTest extends TestCase
{
    private DataAccess $da;
    private array $validParams;
    private array $validParamsMandatory;
    private ?int $testUserId;
    private ?int $testAuthorId;
    private ?int $testPublisherId;
    private ?array $testCategories;

    final public function setUp(): void
    {
        $this->da = new CatalogTitleDataAccess();
        $this->db = Database::instance();

        $this->createTestUser();
        $this->createTestAuthor();
        $this->createTestPublisher();
        $this->createTestCategories();

        $this->validParams = [
            'user' => $this->testUserId,
            'title' => 'TestTitle',
            'author' => $this->testAuthorId,
            'publisher' => $this->testPublisherId,
            'isbn' => '0-312-93033-X',
            'series' => 'Test Crime Series',
            'volume' => 1,
            'pages' => 120,
            'description' => 'Test Title Description',
            'comment' => 'User test comments',
            'categories' => $this->testCategories,
        ];

        $this->validParamsMandatory = [
            'user' => $this->validParams['user'],
            'title' => $this->validParams['title'],
            'author' => null,
            'publisher' => null,
            'isbn' => null,
            'series' => null,
            'volume' => null,
            'pages' => null,
            'description' => null,
            'comment' => null,
            'categories' => array(),
        ];
    }

    private function createTestUser()
    {
        $user = new User();
        $username = 'testUserForTitle' . time();
        $userEmail = $username . '@UserTest.com';
        $user->setUsername($username);
        $user->setEmail($userEmail);
        $user->setPassword('Dup@c1pa');
        $user->registerUser();
        $this->testUserId = $user->getUserId();
    }

    private function createTestAuthor()
    {
        $author = new Author(null, 'TestAuthorFirstNameA', 'TestAuthorLastNameA');
        $this->testAuthorId = $author->getId();
    }

    private function createTestPublisher()
    {
        $publisher = new Publisher(null, 'TestPublisherForTitleTest');
        $this->testPublisherId = $publisher->getId();
    }

    private function createTestCategories()
    {
        $category1 = new Category(null, 'TestCategoryForTitleA');
        $this->testCategories[] = $category1->getId();

        $category2 = new Category(null, 'TestCategoryForTitleB');
        $this->testCategories[] = $category2->getId();
    }



    public function testIsInstanceOfDataAccess()
    {
        $this->assertInstanceOf('MyDramLibrary\Utilities\Database\DataAccess', $this->da);
    }

    public function testThrowDomainExceptionIfTitleKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['title']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfUserKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['user']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfAuthorKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['author']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfPublisherKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['publisher']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfISBNKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['isbn']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfSeriesKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['series']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfVolumeKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['volume']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfPagesKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['pages']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfDescriptionKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['description']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfCommentKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['comment']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfCategoriesKeyIsMissing()
    {
        $this->expectException('DomainException');
        unset($this->validParams['categories']);
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfCategoriesSetToSingleValue()
    {
        $this->expectException('DomainException');
        $this->validParams['categories'] = $this->testCategories[0];
        $this->da->create($this->validParams);
    }

    public function testThrowDomainExceptionIfCategoriesArrayContainsFurtherArrayInsteadOfValue()
    {
        $this->expectException('DomainException');
        $this->validParams['categories'][] = array();
        $this->da->create($this->validParams);
    }

    public function testAcceptTitleWithoutAuthor()
    {
        $this->validParams['author'] = null;
        $this->da->create($this->validParams);
        $this->addToAssertionCount(1);
    }

    public function testAcceptTitleWithoutPublisher()
    {
        $this->validParams['publisher'] = null;
        $this->da->create($this->validParams);
        $this->addToAssertionCount(1);
    }

    public function testNewTitleIdIsInt()
    {
        $this->assertTrue($this->da->create($this->validParams) > 0);
    }

    public function testReturnIdOfCreatedTitle()
    {
        $titleId = $this->da->create($this->validParams);
        $db = Database::instance();
        $titleIdFromDb = $db->run(
            'select id from title where title = \'' . $this->validParams['title'] . '\''
        )->fetchColumn();
        $this->assertEquals($titleId, $titleIdFromDb);
    }

    public function testThrowExceptionIfSelectingTitleByIdNotInDataset()
    {
        $this->expectException('OutOfRangeException');
        $this->da->read(0);
    }

    public function testReturnTitleArrayForExistingId()
    {
        $titleId = $this->da->create($this->validParams);
        $this->assertEquals($this->validParams, $this->da->read($titleId));
    }

    public function testThrowExceptionWhenEditMissingTitle()
    {
        $this->expectException('OutOfRangeException');
        $this->da->update(0, $this->validParams);
    }

    public function testReturnFalseIfUpdatingTitleToSameParameters()
    {
        $titleId = $this->da->create($this->validParams);
        $this->assertFalse($this->da->update($titleId, $this->validParams));
    }

    public function testReturnTrueAfterSuccessfulUpdate()
    {
        $titleId = $this->da->create($this->validParams);
        $this->validParams['title'] .= 'B';
        $this->assertTrue($this->da->update($titleId, $this->validParams));
    }

    public function testCheckUpdateDataMatchUpdateSet()
    {
        $titleId = $this->da->create($this->validParams);
        $this->validParams['title'] .= 'B';
        $this->da->update($titleId, $this->validParams);
        $this->assertTrue($this->da->read($titleId) == $this->validParams);
    }

    public function testThrowExceptionWhenDeletingMissingTitle()
    {
        $this->expectException('OutOfRangeException');
        $this->da->delete(0);
    }

    public function testReturnTrueWhenDeleteTitle()
    {
        $titleId = $this->da->create($this->validParams);
        $this->assertTrue($this->da->delete($titleId));
    }

    public function testConfirmDeletedRecordNotInDataSet()
    {
        $this->expectException('OutOfRangeException');
        $titleId = $this->da->create($this->validParams);
        $this->da->delete($titleId);
        $this->da->read($titleId);
    }

    public function testTitleCategoriesProperlyUpdatedAfterDeletingOne()
    {
        $titleId = $this->da->create($this->validParams);
        unset($this->validParams['categories'][1]);
        $this->da->update($titleId, $this->validParams);
        $this->assertEquals($this->validParams, $this->da->read($titleId));
    }

    public function testTitleCategoriesProperlyUpdatedAfterAddingOne()
    {
        $titleId = $this->da->create($this->validParams);
        $removedCategory = array_pop($this->validParams['categories']);
        $this->da->update($titleId, $this->validParams);
        $this->validParams['categories'][] = $removedCategory;
        $this->da->update($titleId, $this->validParams);
        $this->assertEquals($this->validParams, $this->da->read($titleId));
    }

    public function testTitleCategoriesProperlyUpdatedAfterAddingCategoriesFromNone()
    {
        $this->validParams['categories'] = array();
        $titleId = $this->da->create($this->validParams);
        $this->validParams['categories'] = $this->testCategories;
        $this->da->update($titleId, $this->validParams);
        $this->assertEquals($this->validParams, $this->da->read($titleId));
    }

    public function testTitleCategoriesProperlyUpdatedAfterDeletingAll()
    {
        $titleId = $this->da->create($this->validParams);
        $this->validParams['categories'] = array();
        $this->da->update($titleId, $this->validParams);
        $this->assertEquals($this->validParams, $this->da->read($titleId));
    }

    public function testTitleCreatedWithOnlyMandatoryParamsReturnsSame()
    {
        $titleId = $this->da->create($this->validParamsMandatory);
        $this->assertEquals($this->validParamsMandatory, $this->da->read($titleId));
    }

    public function testTitleCreatedWithMandatoryUpdateToAllReturnsSame()
    {
        $titleId = $this->da->create($this->validParamsMandatory);
        $this->da->update($titleId, $this->validParams);
        $this->assertEquals($this->validParams, $this->da->read($titleId));
    }

    public function testTitleCreatedWithAllUpdatedToMandatoryReturnsSame()
    {
        $titleId = $this->da->create($this->validParams);
        $this->da->update($titleId, $this->validParamsMandatory);
        $this->assertEquals($this->validParamsMandatory, $this->da->read($titleId));
    }

    final public function tearDown(): void
    {
        $db = Database::instance();
        $db->run('delete from title where title like \'TestTitle%\'');
        $db->run('delete from user where id = ' . $this->testUserId);
        $db->run('delete from author where id = ' . $this->testAuthorId);
        $db->run('delete from publisher where id = ' . $this->testPublisherId);
        $db->run('delete from category where category like \'TestCategoryForTitle%\'');
    }
}
