<?php

namespace Tests;

use MyDramLibrary\Catalog\Publisher;
use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Database\Database;

class PublisherTest extends TestCase
{
    private Database $db;
    private int $publisherId;
    private string $publisherName;

    private string $validPublisherName;
    private string $validPublisherName2;

    final public function setUp(): void
    {
        $this->db = Database::instance();
        $this->publisherName = 'PublisherClass_Test_01_'.time();
        $sql = 'insert into publisher (publisher) values (:publisher)';
        $this->db->run($sql, ['publisher' => $this->publisherName]);
        $this->publisherId = $this->db->lastInsertId();

        $this->validPublisherName = 'PublisherClass_Test_'.time();
        $this->validPublisherName2 = $this->validPublisherName.'updated';
    }

    public function testPublisherCreatedWithId()
    {
        $this->assertInstanceOf('MyDramLibrary\Catalog\Publisher', new Publisher($this->publisherId));
    }

    public function testCreatePublisherObjectWithValidName()
    {
        $this->assertInstanceOf('MyDramLibrary\Catalog\Publisher', new Publisher(null, $this->validPublisherName));
    }

    public function testThrowValidatorExceptionWhenSettingPublisherToNotAllowedName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $publisher = new Publisher(null, $this->validPublisherName);
        $invalidName = "Unsupported character for new line \n";
        $publisher->setName($invalidName);
    }

    public function testGetProperNameAfterItWasSet()
    {
        $publisher = new Publisher(null, $this->validPublisherName);
        $this->assertEquals($this->validPublisherName, $publisher->getName());
    }

    public function testThrowExceptionIfCreatingNewPublisherWithoutIdAndName()
    {
        $this->expectException('DomainException');
       new Publisher(null);
    }

    public function testReturnNameForAlreadyExistingPublisher()
    {
        $publisher = new Publisher($this->publisherId);
        $this->assertEquals($this->publisherName, $publisher->getName());
    }

    public function testUpdatedNameIsSavedAndReturned()
    {
        $publisher = new Publisher($this->publisherId);
        $publisher->setName($this->validPublisherName);
        unset ($publisher);

        $publisher2 = new Publisher($this->publisherId);
        $this->assertEquals($this->validPublisherName, $publisher2->getName());
    }

    public function testGetIdOfExistingPublisher()
    {
        $publisher = new Publisher($this->publisherId);
        $this->assertEquals($this->publisherId, $publisher->getId());
    }

    public function testReturnIdOfNewlyCreatedPublisher()
    {
        $publisher = new Publisher(null, $this->validPublisherName);
        $sql = 'select id from publisher where publisher = :publisher';
        $this->assertEquals($this->db->run($sql, ['publisher' => $this->validPublisherName])->fetchColumn(), $publisher->getId());
    }

    public function testGetExistingIdIfSavingDuplicate()
    {
        $publisher = new Publisher(null, $this->publisherName);
        $this->assertEquals($this->publisherId, $publisher->getId());
    }

    public function testNewPublisherImmediatelyUpdatedIsSavedProperly()
    {
        $name = $this->validPublisherName;
        $publisher = new Publisher(null, $name);
        $id = $publisher->getId();

        $publisher->setName($this->validPublisherName2);
        unset($publisher);

        $publisher2 = new Publisher($id);
        $this->assertEquals($this->validPublisherName2, $publisher2->getName());
    }

    public function testNameIsUpdatedForExistingPublisherWhenPassedInConstructor()
    {
        $publisher = new Publisher($this->publisherId, $this->validPublisherName);
        unset($publisher);
        $publisherUpdated = new Publisher($this->publisherId);
        $this->assertEquals($this->validPublisherName, $publisherUpdated->getName());
    }

    public function testThrowOutOfRangeExceptionWhenAskingFOrPublisherWithIncorrectId()
    {
        $this->expectException('OutOfRangeException');
        $publisher = new Publisher(-1);
        $publisher->getName();
    }

    public function testPublisherNewNameIsNotOverwrittenAccidentallyByLoadingDBParams()
    {
        $publisher = new Publisher($this->publisherId, $this->validPublisherName);
        $publisher->getId();
        $this->assertTrue($this->validPublisherName == $publisher->getName());
    }

    public function tearDown(): void
    {
        $sql = 'delete from publisher where id = :id or publisher = :publisher or publisher = :updatedpub';
        $this->db->run($sql, [
            'id' => $this->publisherId,
            'publisher' => $this->validPublisherName,
            'updatedpub' => $this->validPublisherName2
        ]);
        unset($this->publisherId);
        unset($this->publisherName);
    }
}
