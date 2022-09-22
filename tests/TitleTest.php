<?php

namespace Tests;

use MyDramLibrary\Catalog\Author;
use MyDramLibrary\Catalog\Category;
use MyDramLibrary\Catalog\CategoryCollection;
use MyDramLibrary\Catalog\DataAccess\CatalogTitleDataAccess;
use MyDramLibrary\Catalog\Publisher;
use MyDramLibrary\Catalog\Title;
use PHPUnit\Framework\TestCase;
use MyDramLibrary\User\User;
use MyDramLibrary\Utilities\Collection\Collection;
use MyDramLibrary\Utilities\Database\Database;

class TitleTest extends TestCase
{
    private User $user;
    private Author $author;
    private Publisher $publisher;
    private Category $category1;
    private Category $category2;
    private CategoryCollection $categories;

    private string $title = 'TestTitle';
    private string $isbn = '0-312-93033-X';
    private string $series = 'Test Crime Series';
    private int $volume = 1;
    private int $pages = 120;
    private string $description = 'Test Title Description';
    private string $comment = 'Title test comments';

    private ?int $titleId;

    final public function setUp(): void
    {
        $this->createTestUser();
        $this->createTestAuthor();
        $this->createTestPublisher();
        $this->createTestCategories();
        $this->createTestTitleRecord();

        $this->categories = new CategoryCollection();
        $this->categories->addItem($this->category1, $this->category1->getId());
        $this->categories->addItem($this->category2, $this->category2->getId());
    }

    private function createTestUser()
    {
        $db = Database::instance();
        $sql = '
            insert into user (username, email, verification_hash, verified, active, created)
            values (\'testUserForTitle\', \'testUserForTitle@TitleTest.com\', \'qwerty\', 1, 1, now())
        ';
        $db->run($sql);
        $id = $db->lastInsertId();
        $this->user = new User($id);
    }

    private function createTestAuthor()
    {
        $this->author = new Author(null, 'TestAuthorFirstNameA', 'TestAuthorLastNameA');
    }

    private function createTestPublisher()
    {
        $this->publisher = new Publisher(null, 'TestPublisherForTitleTest');
    }

    private function createTestCategories()
    {
        $this->category1 = new Category(null, 'TestCategoryForTitleA');
        $this->category2 = new Category(null, 'TestCategoryForTitleB');
    }

    private function createTestTitleRecord()
    {
        $titleParams = [
            'user' => $this->user->getUserId(),
            'title' => $this->title,
            'author' => $this->author->getId(),
            'publisher' => $this->publisher->getId(),
            'isbn' => $this->isbn,
            'series' => $this->series,
            'volume' => $this->volume,
            'pages' => $this->pages,
            'description' => $this->description,
            'comment' => $this->comment,
            'categories' => array($this->category1->getId(), $this->category2->getId()),
        ];
        $da = new CatalogTitleDataAccess();
        $this->titleId = $da->create($titleParams);
    }

    private function createCustomTestUser(): User
    {
        $db = Database::instance();
        $sql = '
            insert into user (username, email, verification_hash, verified, active, created)
            values (
                \'testUserForTitleB' . time() . '\',
                \'testUserForTitleB' . time() . '@TitleTest.com\',
                \'qwerty\',
                1,
                1,
                now()
            )
        ';
        $db->run($sql);
        return new User($db->lastInsertId());
    }



    public function testTitleCreatedWithId()
    {
        $this->assertInstanceOf('MyDramLibrary\Catalog\Title', new Title($this->titleId));
    }

    public function testCreateNewTitleWithValidMandatoryParams()
    {
        $this->assertInstanceOf('MyDramLibrary\Catalog\Title', new Title(null, $this->user, $this->title));
    }

    public function testThrowDomainExceptionWhenCreatingNewTitleWithoutUser()
    {
        $this->expectException('DomainException');
        new Title(null, null, $this->title);
    }

    public function testThrowDomainExceptionWhenCreatingNewTitleWithoutTitle()
    {
        $this->expectException('DomainException');
        new Title(null, $this->user, null);
    }

    public function testThrowExceptionIfCreatingNewTitleWithoutIdUserAndTitle()
    {
        $this->expectException('DomainException');
        new Title(null);
    }

    public function testThrowValidatorExceptionWhenCreatingNewTitleWithInvalidTitle()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        new Title(null, $this->user, $this->title . "\n");
    }

    public function testGetProperTitleAfterItWasSet()
    {
        $title = new Title(null, $this->user, $this->title);
        $this->assertEquals($this->title, $title->getTitle());
    }

    public function testGetProperTitleAfterItWasModified()
    {
        $title = new Title(null, $this->user, $this->title);
        $updatedTitle = $this->title . 'B';
        $title->setTitle($updatedTitle);
        $this->assertEquals($updatedTitle, $title->getTitle());
    }

    public function testUpdatedTitleIsSavedAndReturned()
    {
        $title = new Title($this->titleId);
        $updatedTitle = $this->title . 'B';
        $title->setTitle($updatedTitle);
        unset($title);

        $title2 = new Title($this->titleId);
        $this->assertEquals($updatedTitle, $title2->getTitle());
    }

    public function testThrowExceptionWhenCreatingNewTitleWithNotExistingUser()
    {
        $this->expectException('DomainException');
        new Title(null, new User(), $this->title);
    }

    public function testThrowExceptionWhenCreatingExistingTitleWithWrongUser()
    {
        $this->expectException('DomainException');
        new Title($this->titleId, $this->createCustomTestUser());
    }

    public function testGetIdOfExistingTitle()
    {
        $title = new Title($this->titleId);
        $this->assertEquals($this->titleId, $title->getId());
    }

    public function testGetIdOfNewlyCreatedTitle()
    {
        $updatedTitle = $this->title . time();
        $title = new Title(null, $this->user, $updatedTitle);
        $sql = 'select id from title where user = :user and title = :title';
        $db = Database::instance();
        $this->assertEquals(
            $db->run($sql, ['user' => $this->user->getUserId(), 'title' => $updatedTitle])->fetchColumn(),
            $title->getId()
        );
    }

    public function testCreatingDuplicateWillCreateNewRecord()
    {
        $title1 = new Title(null, $this->user, $this->title);
        $title2 = new Title(null, $this->user, $this->title);
        $this->assertTrue($title1->getId() != $title2->getId());
    }

    public function testNewTitleImmediatelyUpdatedIsSavedProperly()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();

        $updatedTitle = $this->title . 'B';
        $title->setTitle($updatedTitle);
        unset($title);

        $title2 = new Title($id);
        $this->assertEquals($updatedTitle, $title2->getTitle());
    }

    public function testTitleIsUpdatedForExistingTitleWhenPassedInConstructor()
    {
        $updatedTitle = $this->title . 'B';
        $title = new Title($this->titleId, $this->user, $updatedTitle);
        unset($title);
        $titleUpdated = new Title($this->titleId);
        $this->assertEquals($updatedTitle, $titleUpdated->getTitle());
    }

    public function testThrowOutOfRangeExceptionWhenAskingForTitleWithIncorrectId()
    {
        $this->expectException('OutOfRangeException');
        $title = new Title(-1);
        $title->getId();
    }

    public function testTitleNewTitleIsNotOverwrittenAccidentally()
    {
        $updatedTitle = $this->title . 'B';
        $title = new Title($this->titleId, null, $updatedTitle);
        $title->getId();
        $this->assertEquals($updatedTitle, $title->getTitle());
    }

    public function testTitleIsCreatedWithAuthorInParams()
    {
        $title = new Title(null, $this->user, $this->title, $this->author);
        $id = $title->getId();
        unset($title);

        $title2 = new Title($id);
        $this->assertEquals($this->author->getAuthorName(), $title2->getAuthor()->getAuthorName());
    }

    public function testTitleIsCreatedWithoutAuthorAndAuthorUpdated()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        $title->setAuthor($this->author);
        unset($title);

        $title2 = new Title($id);
        $this->assertEquals($this->author->getAuthorName(), $title2->getAuthor()->getAuthorName());
    }

    public function testTitleIsCreatedWithNewAuthor()
    {
        $title = new Title(null, $this->user, $this->title, new Author(null, 'TestFirstNameAA', 'TestLastNameAA'));
        $id = $title->getId();
        unset($title);

        $title2 = new Title($id);
        $this->assertEquals('TestLastNameAA, TestFirstNameAA', $title2->getAuthor()->getAuthorName());
    }

    public function testTitleIsCreatedAndAuthorUpdated()
    {
        $title = new Title(null, $this->user, $this->title, $this->author);
        $id = $title->getId();

        $this->author->setFirstname('TestFirstNameAA');
        $this->author->setLastname('TestLastNameAA');

        $authorId = $this->author->getId();
        unset($this->author);
        unset($title);
        $this->author = new Author($authorId);

        $title2 = new Title($id);
        $this->assertEquals('TestLastNameAA, TestFirstNameAA', $title2->getAuthor()->getAuthorName());
    }

    public function testTitleIsCreatedWithIdAndNewAuthor()
    {
        $title = new Title(null, $this->user, $this->title, $this->author);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, author: new Author(null, 'TestFirstNameAA', 'TestLastNameAA'));
        unset($title);

        $title = new Title($id);
        $this->assertEquals('TestLastNameAA, TestFirstNameAA', $title->getAuthor()->getAuthorName());
    }

    public function testTitleIsCreatedAndAuthorNotRemovedWithNullConstructor()
    {
        $title = new Title(null, $this->user, $this->title, $this->author);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, author: null);
        unset($title);

        $title = new Title($id);
        $this->assertIsObject($title->getAuthor());
    }

    public function testTitleIsCreatedAndAuthorUpdatedToNullWithSetter()
    {
        $title = new Title(null, $this->user, $this->title, $this->author);
        $id = $title->getId();
        $title->setAuthor(null);
        unset($title);

        $title = new Title($id);
        $this->assertNull($title->getAuthor());
    }

    public function testTitleIsCreatedWithPublisherInParams()
    {
        $title = new Title(null, $this->user, $this->title, $this->author, $this->publisher);
        $id = $title->getId();
        unset($title);

        $title2 = new Title($id);
        $this->assertEquals($this->publisher->getName(), $title2->getPublisher()->getName());
    }

    public function testTitleIsCreatedWithoutPublisherAndPublisherUpdated()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        $title->setPublisher($this->publisher);
        unset($title);

        $title2 = new Title($id);
        $this->assertEquals($this->publisher->getName(), $title2->getPublisher()->getName());
    }

    public function testTitleIsCreatedWithNewPublisher()
    {
        $title = new Title(null, $this->user, $this->title, publisher: new Publisher(null, 'TestPublisherAA'));
        $id = $title->getId();
        unset($title);

        $title2 = new Title($id);
        $this->assertEquals('TestPublisherAA', $title2->getPublisher()->getName());
    }

    public function testTitleIsCreatedAndPublisherUpdated()
    {
        $title = new Title(null, $this->user, $this->title, publisher: $this->publisher);
        $id = $title->getId();

        $this->publisher->setName('TestPublisherAA');

        $publisherId = $this->publisher->getId();
        unset($this->publisher);
        unset($title);
        $this->publisher = new Publisher($publisherId);

        $title2 = new Title($id);
        $this->assertEquals('TestPublisherAA', $title2->getPublisher()->getName());
    }

    public function testTitleIsCreatedWithIdAndNewPublisher()
    {
        $title = new Title(null, $this->user, $this->title, publisher: $this->publisher);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, publisher: new Publisher(null, 'TestPublisherAA'));
        unset($title);

        $title = new Title($id);
        $this->assertEquals('TestPublisherAA', $title->getPublisher()->getName());
    }

    public function testTitleIsCreatedAndPublisherNotRemovedWithNullConstructor()
    {
        $title = new Title(null, $this->user, $this->title, publisher: $this->publisher);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, publisher: null);
        unset($title);

        $title = new Title($id);
        $this->assertIsObject($title->getPublisher());
    }

    public function testTitleIsCreatedAndPublisherUpdatedToNullWithSetter()
    {
        $title = new Title(null, $this->user, $this->title, publisher: $this->publisher);
        $id = $title->getId();
        $title->setPublisher(null);
        unset($title);

        $title = new Title($id);
        $this->assertNull($title->getPublisher());
    }

    public function testTitleIsCreatedWithISBNInParams()
    {
        $title = new Title(null, $this->user, $this->title, isbn: $this->isbn);
        $id = $title->getId();
        unset($title);

        $title = new Title($id);
        $this->assertEquals($this->isbn, $title->getISBN());
    }

    public function testTitleIsCreatedWithoutISBNAndISBNUpdated()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        $title->setISBN($this->isbn);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($this->isbn, $title->getISBN());
    }

    public function testTitleIsCreatedWithIdAndISBNUpdatedThroughConstructor()
    {
        $title = new Title(null, $this->user, $this->title, isbn: $this->isbn);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, isbn: '978-3-16-148410-0');
        unset($title);

        $title = new Title($id);
        $this->assertEquals('978-3-16-148410-0', $title->getISBN());
    }

    public function testTitleIsCreatedAndISBNNotRemovedWithNullConstructor()
    {
        $title = new Title(null, $this->user, $this->title, isbn: $this->isbn);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, isbn: null);
        unset($title);

        $title = new Title($id);
        $this->assertFalse(null == $title->getISBN());
    }

    public function testTitleIsCreatedAndISBNUpdatedToNullWithSetter()
    {
        $title = new Title(null, $this->user, $this->title, isbn: $this->isbn);
        $id = $title->getId();
        $title->setISBN(null);
        unset($title);

        $title = new Title($id);
        $this->assertNull($title->getISBN());
    }

    public function testTitleIsCreatedWithInvalidISBNAndExceptionThrown()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        new Title(null, $this->user, $this->title, isbn: 'invalid value');
    }

    public function testTitleIsCreatedWithSeriesInParams()
    {
        $title = new Title(null, $this->user, $this->title, series: $this->series);
        $id = $title->getId();
        unset($title);

        $title = new Title($id);
        $this->assertEquals($this->series, $title->getSeries());
    }

    public function testTitleIsCreatedWithoutSeriesAndSeriesUpdated()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        $title->setSeries($this->series);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($this->series, $title->getSeries());
    }

    public function testTitleIsCreatedWithIdAndSeriesUpdatedThroughConstructor()
    {
        $title = new Title(null, $this->user, $this->title, series: $this->series);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, series: 'Test Crime Series B');
        unset($title);

        $title = new Title($id);
        $this->assertEquals('Test Crime Series B', $title->getSeries());
    }

    public function testTitleIsCreatedAndSeriesNotRemovedWithNullConstructor()
    {
        $title = new Title(null, $this->user, $this->title, series: $this->series);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, series: null);
        unset($title);

        $title = new Title($id);
        $this->assertFalse(null == $title->getSeries());
    }

    public function testTitleIsCreatedAndSeriesUpdatedToNullWithSetter()
    {
        $title = new Title(null, $this->user, $this->title, series: $this->series);
        $id = $title->getId();
        $title->setSeries(null);
        unset($title);

        $title = new Title($id);
        $this->assertNull($title->getSeries());
    }

    public function testTitleIsCreatedWithInvalidSeriesAndExceptionThrown()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        new Title(null, $this->user, $this->title, series: "invalid series\n");
    }

    public function testTitleIsCreatedWithVolumeInParams()
    {
        $title = new Title(null, $this->user, $this->title, volume: $this->volume);
        $id = $title->getId();
        unset($title);

        $title = new Title($id);
        $this->assertSame($this->volume, $title->getVolume());
    }

    public function testTitleIsCreatedWithoutVolumeAndVolumeUpdated()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        $title->setVolume($this->volume);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($this->volume, $title->getVolume());
    }

    public function testTitleIsCreatedWithIdAndVolumeUpdatedThroughConstructor()
    {
        $title = new Title(null, $this->user, $this->title, volume: $this->volume);
        $id = $title->getId();
        unset($title);

        $volume2 = $this->volume + 1;
        $title = new Title($id, volume: $volume2);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($volume2, $title->getVolume());
    }

    public function testTitleIsCreatedAndVolumeNotRemovedWithNullConstructor()
    {
        $title = new Title(null, $this->user, $this->title, volume: $this->volume);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, volume: null);
        unset($title);

        $title = new Title($id);
        $this->assertFalse(null == $title->getVolume());
    }

    public function testTitleIsCreatedAndVolumeUpdatedToNullWithSetter()
    {
        $title = new Title(null, $this->user, $this->title, volume: $this->volume);
        $id = $title->getId();
        $title->setVolume(null);
        unset($title);

        $title = new Title($id);
        $this->assertNull($title->getVolume());
    }

    public function testTitleIsCreatedWithInvalidVolumeAndExceptionThrown()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        new Title(null, $this->user, $this->title, volume: 0);
    }

    public function testTitleIsCreatedWithPagesInParams()
    {
        $title = new Title(null, $this->user, $this->title, pages: $this->pages);
        $id = $title->getId();
        unset($title);

        $title = new Title($id);
        $this->assertSame($this->pages, $title->getPages());
    }

    public function testTitleIsCreatedWithoutPagesAndPagesUpdated()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        $title->setPages($this->pages);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($this->pages, $title->getPages());
    }

    public function testTitleIsCreatedWithIdAndPagesUpdatedThroughConstructor()
    {
        $title = new Title(null, $this->user, $this->title, pages: $this->pages);
        $id = $title->getId();
        unset($title);

        $pages2 = $this->pages + 1;
        $title = new Title($id, pages: $pages2);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($pages2, $title->getPages());
    }

    public function testTitleIsCreatedAndPagesNotRemovedWithNullConstructor()
    {
        $title = new Title(null, $this->user, $this->title, pages: $this->pages);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, pages: null);
        unset($title);

        $title = new Title($id);
        $this->assertFalse(null == $title->getPages());
    }

    public function testTitleIsCreatedAndPagesUpdatedToNullWithSetter()
    {
        $title = new Title(null, $this->user, $this->title, pages: $this->pages);
        $id = $title->getId();
        $title->setPages(null);
        unset($title);

        $title = new Title($id);
        $this->assertNull($title->getPages());
    }

    public function testTitleIsCreatedWithInvalidPagesAndExceptionThrown()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        new Title(null, $this->user, $this->title, pages: -1);
    }

    public function testTitleIsCreatedWithDescriptionInParams()
    {
        $title = new Title(null, $this->user, $this->title, description: $this->description);
        $id = $title->getId();
        unset($title);

        $title = new Title($id);
        $this->assertSame($this->description, $title->getDescription());
    }

    public function testTitleIsCreatedWithoutDescriptionAndDescriptionUpdated()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        $title->setDescription($this->description);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($this->description, $title->getDescription());
    }

    public function testTitleIsCreatedWithIdAndDescriptionUpdatedThroughConstructor()
    {
        $title = new Title(null, $this->user, $this->title, description: $this->description);
        $id = $title->getId();
        unset($title);

        $desc2 = $this->description .= '2';
        $title = new Title($id, description: $desc2);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($desc2, $title->getDescription());
    }

    public function testTitleIsCreatedAndDescriptionNotRemovedWithNullConstructor()
    {
        $title = new Title(null, $this->user, $this->title, description: $this->description);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, description: null);
        unset($title);

        $title = new Title($id);
        $this->assertFalse(null == $title->getDescription());
    }

    public function testTitleIsCreatedAndDescriptionUpdatedToNullWithSetter()
    {
        $title = new Title(null, $this->user, $this->title, description: $this->description);
        $id = $title->getId();
        $title->setDescription(null);
        unset($title);

        $title = new Title($id);
        $this->assertNull($title->getDescription());
    }

    public function testTitleIsCreatedWithInvalidDescriptionAndExceptionThrown()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        new Title(null, $this->user, $this->title, description: '');
    }

    public function testTitleIsCreatedWithCommentInParams()
    {
        $title = new Title(null, $this->user, $this->title, comment: $this->comment);
        $id = $title->getId();
        unset($title);

        $title = new Title($id);
        $this->assertSame($this->comment, $title->getComment());
    }

    public function testTitleIsCreatedWithoutCommentAndCommentUpdated()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        $title->setComment($this->comment);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($this->comment, $title->getComment());
    }

    public function testTitleIsCreatedWithIdAndCommentUpdatedThroughConstructor()
    {
        $title = new Title(null, $this->user, $this->title, comment: $this->comment);
        $id = $title->getId();
        unset($title);

        $comments2 = $this->comment .= '2';
        $title = new Title($id, comment: $comments2);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($comments2, $title->getComment());
    }

    public function testTitleIsCreatedAndCommentNotRemovedWithNullConstructor()
    {
        $title = new Title(null, $this->user, $this->title, comment: $this->comment);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, comment: null);
        unset($title);

        $title = new Title($id);
        $this->assertFalse(null == $title->getComment());
    }

    public function testTitleIsCreatedAndCommentUpdatedToNullWithSetter()
    {
        $title = new Title(null, $this->user, $this->title, comment: $this->comment);
        $id = $title->getId();
        $title->setComment(null);
        unset($title);

        $title = new Title($id);
        $this->assertNull($title->getComment());
    }

    public function testTitleIsCreatedWithInvalidCommentAndExceptionThrown()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        for ($i = 0, $comment = ''; $i < 10001; $i++) {
            $comment .= 'a';
        }
        new Title(null, $this->user, $this->title, comment: $comment);
    }

    public function testTitleIsCreatedWithCategoryCollectionInParams()
    {
        $title = new Title(null, $this->user, $this->title, categories: $this->categories);
        $id = $title->getId();
        unset($title);

        $title2 = new Title($id);

        $baseCategories = array();
        foreach ($this->categories as $key => $category) {
            $baseCategories[$key] = $category->getName();
        }

        $checkCategories = array();
        foreach ($title2->getCategories() as $key => $category) {
            $checkCategories[$key] = $category->getName();
        }

        $this->assertEquals($baseCategories, $checkCategories);
    }

    public function testTitleIsCreatedWithoutCategoriesAndCategoriesUpdated()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        $title->setCategories($this->categories);
        unset($title);

        $title2 = new Title($id);

        $baseCategories = array();
        foreach ($this->categories as $key => $category) {
            $baseCategories[$key] = $category->getName();
        }

        $checkCategories = array();
        foreach ($title2->getCategories() as $key => $category) {
            $checkCategories[$key] = $category->getName();
        }

        $this->assertEquals($baseCategories, $checkCategories);
    }

    public function testTitleIsCreatedWithIdAndCategoriesUpdatedWithConstructor()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, categories: $this->categories);
        unset($title);

        $title2 = new Title($id);

        $baseCategories = array();
        foreach ($this->categories as $key => $category) {
            $baseCategories[$key] = $category->getName();
        }

        $checkCategories = array();
        foreach ($title2->getCategories() as $key => $category) {
            $checkCategories[$key] = $category->getName();
        }

        $this->assertEquals($baseCategories, $checkCategories);
    }

    public function testTitleIsCreatedWithIdAndCategoriesUpdatedWithSetter()
    {
        $title = new Title(null, $this->user, $this->title);
        $id = $title->getId();
        unset($title);

        $title = new Title($id);
        $title->setCategories($this->categories);
        unset($title);

        $title2 = new Title($id);

        $baseCategories = array();
        foreach ($this->categories as $key => $category) {
            $baseCategories[$key] = $category->getName();
        }

        $checkCategories = array();
        foreach ($title2->getCategories() as $key => $category) {
            $checkCategories[$key] = $category->getName();
        }

        $this->assertEquals($baseCategories, $checkCategories);
    }

    public function testTitleIsCreatedAndCategoriesNotRemovedWithNullConstructor()
    {
        $title = new Title(null, $this->user, $this->title, categories: $this->categories);
        $id = $title->getId();
        unset($title);

        $title = new Title($id, categories: null);
        unset($title);

        $title = new Title($id);
        $this->assertEquals($this->categories->length(), $title->getCategories()->length());
    }

    public function testTitleIsCreatedAndCategoriesRemovedWithSetter()
    {
        $title = new Title(null, $this->user, $this->title, categories: $this->categories);
        $id = $title->getId();
        $title->setCategories(new CategoryCollection());
        unset($title);

        $title = new Title($id);
        $this->assertEquals(0, $title->getCategories()->length());
    }

    public function testAdditionalCategoryIsAddedToTitleWithChangeOnCollectionObject()
    {
        $this->categories->removeItem($this->category2->getId());
        $title = new Title($this->titleId, categories: $this->categories);
        $this->categories->addItem($this->category2, $this->category2->getId());
        unset($title);

        $title2 = new Title($this->titleId);

        $baseCategories = array();
        foreach ($this->categories as $key => $category) {
            $baseCategories[$key] = $category->getName();
        }

        $checkCategories = array();
        foreach ($title2->getCategories() as $key => $category) {
            $checkCategories[$key] = $category->getName();
        }

        $this->assertEquals($baseCategories, $checkCategories);
    }

    public function testCategoryIsRemovedFromTitleWithChangeOnCollectionObject()
    {
        $title = new Title($this->titleId, categories: $this->categories);
        $this->categories->removeItem($this->category2->getId());
        unset($title);

        $title2 = new Title($this->titleId);

        $baseCategories = array();
        foreach ($this->categories as $key => $category) {
            $baseCategories[$key] = $category->getName();
        }

        $checkCategories = array();
        foreach ($title2->getCategories() as $key => $category) {
            $checkCategories[$key] = $category->getName();
        }

        $this->assertEquals($baseCategories, $checkCategories);
    }

    public function testTitleCreatedWithAllValuesAndReturnSameValuesForAllParams()
    {
        $title = new Title(
            $this->titleId,
            null,
            $this->title,
            $this->author,
            $this->publisher,
            $this->isbn,
            $this->series,
            $this->volume,
            $this->pages,
            $this->description,
            $this->comment,
            $this->categories
        );
        unset($title);

        $title2 = new Title(
            null,
            $this->user,
            $this->title,
            $this->author,
            $this->publisher,
            $this->isbn,
            $this->series,
            $this->volume,
            $this->pages,
            $this->description,
            $this->comment,
            $this->categories
        );
        $id = $title2->getId();
        unset($title2);

        $collection = new Collection();
        $collection->addItem(new Title($this->titleId), $this->titleId);
        $collection->addItem(new Title($id), $id);

        $checkArray = array();
        foreach ($collection as $key => $title) {
            $checkArray[$key]['user'] = $title->getUser()->getUserId();
            $checkArray[$key]['title'] = $title->getTitle();
            $checkArray[$key]['author'] = $title->getAuthor()->getAuthorName();
            $checkArray[$key]['publisher'] = $title->getPublisher()->getName();
            $checkArray[$key]['isbn'] = $title->getISBN();
            $checkArray[$key]['series'] = $title->getSeries();
            $checkArray[$key]['volume'] = $title->getVolume();
            $checkArray[$key]['pages'] = $title->getPages();
            $checkArray[$key]['description'] = $title->getDescription();
            $checkArray[$key]['comment'] = $title->getComment();

            foreach ($title->getCategories() as $category) {
                $checkArray[$key]['categories'][$category->getId()] = $category->getName();
            }
        }

        $this->assertEquals($checkArray[$this->titleId], $checkArray[$id]);
    }

    public function testTitleCreatedWithAllValuesAndReturnSameValuesInArray()
    {
        $title = new Title(
            $this->titleId,
            null,
            $this->title,
            $this->author,
            $this->publisher,
            $this->isbn,
            $this->series,
            $this->volume,
            $this->pages,
            $this->description,
            $this->comment,
            $this->categories
        );

        $categories = array();
        foreach ($title->getCategories() as $category) {
            $categories[] = $category->getName();
        }
        $checkArray = [
            'id'              => $title->getId(),
            'title'           => $title->getTitle(),
            'authorFirstname' => $title->getAuthor()->getFirstname(),
            'authorLastname'  => $title->getAuthor()->getLastname(),
            'author'          => $title->getAuthor()->getAuthorName(),
            'publisher'       => $title->getPublisher()->getName(),
            'isbn'            => $title->getISBN(),
            'series'          => $title->getSeries(),
            'volume'          => $title->getVolume(),
            'pages'           => $title->getPages(),
            'description'     => $title->getDescription(),
            'comment'         => $title->getComment(),
            'category'        => $categories,
        ];

        $titleArray = $title->getDataArray();

        $this->assertEquals($checkArray, $titleArray);
    }

    public function testThrowExceptionWhenCreatingTitleAfterDeleted()
    {
        $this->expectException('Exception');
        $title = new Title($this->titleId);
        $title->deleteTitle();
        unset($title);
        (new Title($this->titleId))->getId();
    }

    public function tearDown(): void
    {
        $db = Database::instance();

        $db->run('delete from title where title like \'TestTitle%\'');
        $db->run('delete from user where id = ' . $this->user->getUserId());
        $db->run('delete from user where username like \'testUserForTitle%\'');
        $db->run('delete from author where id = ' . $this->author->getId());
        $db->run('delete from author where firstname like \'TestFirstName%\'');
        $db->run('delete from publisher where id = ' . $this->publisher->getId());
        $db->run('delete from publisher where publisher like \'TestPublisherAA%\'');
        $db->run('delete from category where category like \'TestCategoryForTitle%\'');
    }
}
