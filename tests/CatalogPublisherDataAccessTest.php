<?php

use MyDramLibrary\Catalog\DataAccess\CatalogPublisherDataAccess;
use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Database\Database;

class CatalogPublisherDataAccessTest extends TestCase
{
    public function testIsInstanceOfDataAccess()
    {
        $this->assertInstanceOf('MyDramLibrary\Utilities\Database\DataAccess', new CatalogPublisherDataAccess());
    }

    public function testThrowValidatorExceptionIfAddingPublisherWithoutName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $dataAccess = new CatalogPublisherDataAccess();
        $dataAccess->create(['publisher' => '']);
    }

    public function testThrowValidatorExceptionIfAddingPublisherWithNameLongerThan255()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $dataAccess = new CatalogPublisherDataAccess();
        $publisher['publisher'] = '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890';
        $dataAccess->create($publisher);
    }

    public function testReturnExistingIdWhenAddingExistingPublisherName()
    {
        $dataAccess = new CatalogPublisherDataAccess();
        $publisher['publisher'] = 'TestPublisher01_' . time();
        $id1 = $dataAccess->create($publisher);
        $id2 = $dataAccess->create($publisher);
        $this->assertSame($id1, $id2);
    }

    public function testReturnIdOfCreatedPublisher()
    {
        $dataAccess = new CatalogPublisherDataAccess();
        $publisher['publisher'] = 'TestPublisher02_' . time();
        $publisherId = $dataAccess->create($publisher);

        $db = Database::instance();
        $id = $db->run('select id from publisher where publisher = \'' . $publisher['publisher'] . '\'')->fetchColumn();
        $this->assertEquals($publisherId, $id);
    }

    public function testThrowExceptionIfSelectingMissingPublisherById()
    {
        $this->expectException('OutOfRangeException');
        $dataAccess = new CatalogPublisherDataAccess();
        $dataAccess->read(0);
    }

    public function testReturnPublisherNameForExistingId()
    {
        $dataAccess = new CatalogPublisherDataAccess();
        $publisher['publisher'] = 'TestPublisher03_' . time();
        $publisherId = $dataAccess->create($publisher);

        unset($dataAccess);

        $dataAccess = new CatalogPublisherDataAccess();
        $this->assertEquals($publisher['publisher'], $dataAccess->read($publisherId)['publisher']);
    }

    public function testThrowExceptionWhenEditMissingPublisher()
    {
        $this->expectException('OutOfRangeException');
        $dataAccess = new CatalogPublisherDataAccess();
        $dataAccess->update(0, ['publisher' => 'newname']);
    }

    public function testThrowValidatorExceptionIfUpdatingToWrongName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $dataAccess = new CatalogPublisherDataAccess();
        $publisher['publisher'] = 'TestPublisher04_' . time();
        $publisherId = $dataAccess->create($publisher);

        $wrongPublisher['publisher'] = '';
        $dataAccess->update($publisherId, $wrongPublisher);
    }

    public function testReturnFalseIfUpdatingPublisherToSameName()
    {
        $dataAccess = new CatalogPublisherDataAccess();
        $publisher['publisher'] = 'TestPublisher05_' . time();
        $publisherId = $dataAccess->create($publisher);
        $this->assertFalse($dataAccess->update($publisherId, $publisher));
    }

    public function testThrowPDOExceptionWhenUpdatingToSomeExistingName()
    {
        $this->expectException('PDOException');
        $dataAccess = new CatalogPublisherDataAccess();
        $publisher1['publisher'] = 'TestPublisher06_' . time();
        $publisherId1 = $dataAccess->create($publisher1);

        $publisher2['publisher'] = 'TestPublisher07_' . time();
        $dataAccess->create($publisher2);

        $dataAccess->update($publisherId1, $publisher2);        
    }

    public function testReturnTrueAfterSuccessfulUpdate()
    {
        $dataAccess = new CatalogPublisherDataAccess();
        $publisher['publisher'] = 'TestPublisher08_' . time();
        $publisherId = $dataAccess->create($publisher);

        $publisher2['publisher'] = 'TestPublisher09_' . time();
        $this->assertTrue($dataAccess->update($publisherId, $publisher2));
    }

    public function testThrowDomainExceptionIfPublisherKeyMissingInGivenParamToCreate()
    {
        $this->expectException('DomainException');
        $dataAccess = new CatalogPublisherDataAccess();
        $publisher['name'] = 'TestPublisher10_' . time();
        $dataAccess->create($publisher);
    }

    public function testThrowInvalidArgumentExceptionWhenReadingPublisherWithNotIntegerKey()
    {
        $this->expectException('InvalidArgumentException');
        $dataAccess = new CatalogPublisherDataAccess();
        $dataAccess->read('not integer');
    }

    public function testThrowInvalidArgumentExceptionWhenUpdatingPublisherWithNotIntegerKey()
    {
        $this->expectException('InvalidArgumentException');
        $dataAccess = new CatalogPublisherDataAccess();
        $dataAccess->update('not integer', ['publisher' => 'pubname']);
    }

    public function testThrowDomainExceptionIfPublisherKeyMissingInGivenParamToUpdate()
    {
        $this->expectException('DomainException');
        $dataAccess = new CatalogPublisherDataAccess();
        $incorrectKeyName['name'] = 'TestPublisher10_' . time();
        $correctKeyName['publisher'] = $incorrectKeyName['name'];
        $id = $dataAccess->create($correctKeyName);
        $dataAccess->update($id, $incorrectKeyName);
    }

    public function testThrowInvalidArgumentExceptionWhenDeletePublisherWithNotIntegerKey()
    {
        $this->expectException('InvalidArgumentException');
        $dataAccess = new CatalogPublisherDataAccess();
        $dataAccess->delete('not integer');
    }

    public function tearDown(): void
    {
        $db = Database::instance();
        $db->run('delete from publisher where publisher like \'TestPublisher%\'');
    }
}
