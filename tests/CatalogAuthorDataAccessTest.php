<?php

namespace Tests;

use MyDramLibrary\Catalog\DataAccess\CatalogAuthorDataAccess;
use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Database\DataAccess;
use MyDramLibrary\Utilities\Database\Database;

class CatalogAuthorDataAccessTest extends TestCase
{
    private DataAccess $da;

    final public function setUp(): void
    {
        $this->da = new CatalogAuthorDataAccess();
    }

    public function testIsInstanceOfDataAccess()
    {
        $this->assertInstanceOf('MyDramLibrary\Utilities\Database\DataAccess', new CatalogAuthorDataAccess());
    }

    public function testThrowValidatorExceptionIfAddingAuthorWithoutNames()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $this->da->create(['firstname' => '', 'lastname' => '']);
    }

    public function testThrowValidatorExceptionIfAddingAuthorWithFirstNameLongerThan255()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $author['firstname'] = 'Test John';
        $author['lastname'] = '';
        for ($i = 0; $i <= 260; $i++) {
            $author['lastname'] .= 'A';
        }
        $this->da->create($author);
    }

    public function testThrowValidatorExceptionIfAddingAuthorWithLastNameLongerThan255()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $author['lastname'] = 'TestAuthorA';
        $author['firstname'] = '';
        for ($i = 0; $i <= 260; $i++) {
            $author['firstname'] .= 'A';
        }
        $this->da->create($author);
    }

    public function testReturnExistingIdWhenAddingExistingAuthor()
    {
        $author = [
            'firstname' => 'TestFirstNameA',
            'lastname' => 'TestAuthorA',
        ];
        $id1 = $this->da->create($author);
        $id2 = $this->da->create($author);
        $this->assertSame($id1, $id2);
    }

    public function testReturnIdOfCreatedAuthor()
    {
        $author = [
            'firstname' => 'TestFirstNameB',
            'lastname' => 'TestAuthorB',
        ];
        $authorId = $this->da->create($author);

        $db = Database::instance();
        $id = $db->run('select id from author where firstName = \''.$author['firstname'].'\' and lastName = \''.$author['lastname'].'\'')->fetchColumn();
        $this->assertEquals($authorId, $id);
    }

    public function testThrowExceptionIfSelectingMissingAuthorById()
    {
        $this->expectException('OutOfRangeException');
        $this->da->read(0);
    }

    public function testReturnAuthorForExistingId()
    {
        $author = [
            'firstname' => 'TestFirstNameC',
            'lastname' => 'TestAuthorC',
        ];
        $authorId = $this->da->create($author);
        
        unset($this->da);
        $da = new CatalogAuthorDataAccess();

        $this->assertEquals($author, $da->read($authorId));
    }

    public function testThrowExceptionWhenEditMissingAuthor()
    {
        $this->expectException('OutOfRangeException');
        $this->da->update(0, ['firstname' => 'TestFirstNameD', 'lastname' => 'TestAuthorD']);
    }

    public function testThrowValidatorExceptionIfUpdatingToWrongFirstName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $author = [
            'firstname' => 'TestFirstNameE',
            'lastname' => 'TestAuthorE',
        ];
        $authorId = $this->da->create($author);

        $author['firstname'] = '';
        $this->da->update($authorId, $author);
    }

    public function testThrowValidatorExceptionIfUpdatingToWrongLastName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $author = [
            'firstname' => 'TestFirstNameF',
            'lastname' => 'TestAuthorF',
        ];
        $authorId = $this->da->create($author);

        $author['lastname'] = "Invalid Lastn@me contains \r";
        $this->da->update($authorId, $author);
    }

    public function testReturnFalseIfUpdatingAuthorToSameNames()
    {
        $author = [
            'firstname' => 'TestFirstNameG',
            'lastname' => 'TestAuthorG',
        ];
        $authorId = $this->da->create($author);
        $this->assertFalse($this->da->update($authorId, $author));
    }

    public function testThrowPDOExceptionWhenUpdatingToSomeExistingNames()
    {
        $this->expectException('PDOException');
        
        $author1 = [
            'firstname' => 'TestFirstNameH',
            'lastname' => 'TestAuthorH',
        ];
        $id1 = $this->da->create($author1);
        
        $author2 = [
            'firstname' => 'TestFirstNameI',
            'lastname' => 'TestAuthorI',
        ];
        $id2 = $this->da->create($author2);
        
        $this->da->update($id1, $author2);        
    }

    public function testReturnTrueAfterSuccessfulUpdate()
    {
        $author = [
            'firstname' => 'TestFirstNameJ',
            'lastname' => 'TestAuthorJ',
        ];
        $id = $this->da->create($author);

        $author2 = [
            'firstname' => 'TestFirstNameK',
            'lastname' => 'TestAuthorK',
        ];
        $this->assertTrue($this->da->update($id, $author2));
    }

    public function testThrowDomainExceptionIfAuthorKeyMissingInGivenParamToCreate()
    {
        $this->expectException('DomainException');
        $author['name'] = 'TestFirstNameL';
        $this->da->create($author);
    }

    public function testThrowInvalidArgumentExceptionWhenReadingAuthorWithNotIntegerKey()
    {
        $this->expectException('InvalidArgumentException');
        $this->da->read('not integer');
    }

    public function testThrowInvalidArgumentExceptionWhenUpdatingAuthorWithNotIntegerKey()
    {
        $this->expectException('InvalidArgumentException');
        $this->da->update('not integer', ['firstname' => 'Ff', 'lastname' => 'Ll']);
    }

    public function testThrowDomainExceptionIfAuthorKeyMissingInGivenParamToUpdate()
    {
        $this->expectException('DomainException');
        $invalidAuthor['name'] = 'TestFirstNameL';
        $validAuthor = [
            'firstname' => 'TestFirstNameL',
            'lastname' => 'TestAuthorL',
        ];
        $id = $this->da->create($validAuthor);
        $this->da->update($id, $invalidAuthor);
    }

    public function testThrowInvalidArgumentExceptionWhenDeleteAuthorWithNotIntegerKey()
    {
        $this->expectException('InvalidArgumentException');
        $this->da->delete('not integer');
    }

    public function testThrowValidatorExceptionWhenFirstNameHasIncorrectChars()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $author = [
            'firstname' => "TestFirstNameM1\n",
            'lastname' => 'TestAuthorM',
        ];
        $this->da->create($author);
    }

    public function testThrowValidatorExceptionWhenLastNameHasIncorrectChars()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $author = [
            'firstname' => 'TestFirstNameN',
            'lastname' => "TestAuthorN\t",
        ];
        $this->da->create($author);
    }

    public function testCreateAuthorWithAllAllowedCharsInNames()
    {
        $author = [
            'firstname' => "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNMęóąśłżźćńĘÓĄŚŁŻŹĆŃ \' koniec",
            'lastname' => "TestAuthorOqwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNMęóąśłżźćńĘÓĄŚŁŻŹĆŃ ' koniec",
        ];
        $this->assertTrue($this->da->create($author) >= 1);
    }

    final public function tearDown(): void
    {
        $db = Database::instance();
        $db->run('delete from author where lastname like \'TestAuthor%\'');
    }
}
