<?php

namespace Tests;

use MyDramLibrary\Catalog\Author;
use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Database\Database;

class AuthorTest extends TestCase
{
    private Database $db;
    private $id;
    private string $fname;
    private string $lname;

    private string $validFname;
    private string $validFname2;
    private string $validLname;
    private string $validLname2;

    final public function setUp(): void
    {
        $this->db = Database::instance();
        $this->fname = 'TestAuthorFNameA';
        $this->lname = 'TestAuthorLNameA';
        $sql = 'insert into author (firstName, lastName) values (:fname, :lname)';
        $this->db->run($sql, ['fname' => $this->fname, 'lname' => $this->lname]);
        $this->id = $this->db->lastInsertId();

        $this->validFname = 'TestAuthorFirstNameA';
        $this->validFname2 = $this->validFname . 'updated';
        $this->validLname = 'TestAuthorLastNameA';
        $this->validLname2 = $this->validLname . 'updated';
    }

    public function testAuthorCreatedWithId()
    {
        $this->assertInstanceOf('MyDramLibrary\Catalog\Author', new Author($this->id));
    }

    public function testCreateAuthorObjectWithValidNames()
    {
        $this->assertInstanceOf('MyDramLibrary\Catalog\Author', new Author(null, $this->validFname, $this->validLname));
    }

    public function testThrowValidatorExceptionWhenSettingAuthorToNotAllowedFirstName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $author = new Author(null, $this->validFname, $this->validLname);
        $invalidName = "invalidname contains nl \n";
        $author->setFirstname($invalidName);
    }

    public function testThrowValidatorExceptionWhenSettingAuthorToNotAllowedLastName()
    {
        $this->expectException('MyDramLibrary\CustomException\ValidatorException');
        $author = new Author(null, $this->validFname, $this->validLname);
        $invalidName = "invalid last name contains \t";
        $author->setLastname($invalidName);
    }

    public function testGetProperFirstNameAfterItWasSet()
    {
        $author = new Author(null, $this->validFname, $this->validLname);
        $this->assertEquals($this->validFname, $author->getFirstname());
    }

    public function testGetProperLastNameAfterItWasSet()
    {
        $author = new Author(null, $this->validFname, $this->validLname);
        $this->assertEquals($this->validLname, $author->getLastname());
    }

    public function testGetProperFullNameAfterItWasSet()
    {
        $author = new Author(null, $this->validFname, $this->validLname);
        $this->assertEquals($this->validLname . ', ' . $this->validFname, $author->getAuthorName());
    }

    public function testThrowExceptionIfCreatingNewAuthorWithoutIdAndNames()
    {
        $this->expectException('DomainException');
        new Author(null);
    }

    public function testThrowExceptionIfCreatingNewAuthorWithoutIdAndLastName()
    {
        $this->expectException('DomainException');
        new Author(null, $this->validFname);
    }

    public function testThrowExceptionIfCreatingNewAuthorWithoutIdAndFirstName()
    {
        $this->expectException('DomainException');
        new Author(null, lastname: $this->validLname);
    }

    public function testThrowExceptionIfCreatingNewAuthorWithoutIdAndFirstNameNull()
    {
        $this->expectException('DomainException');
        new Author(null, null, $this->validLname);
    }

    public function testThrowExceptionIfCreatingNewAuthorWithoutIdAndLastNameNull()
    {
        $this->expectException('DomainException');
        new Author(null, $this->validFname, null);
    }

    public function testReturnNameForAlreadyExistingAuthor()
    {
        $author = new Author($this->id);
        $this->assertEquals($this->lname . ', ' . $this->fname, $author->getAuthorName());
    }

    public function testUpdatedFirstNameIsSavedAndReturned()
    {
        $author = new Author($this->id);
        $author->setFirstname($this->validFname);
        unset($author);

        $author2 = new Author($this->id);
        $this->assertEquals($this->validFname, $author2->getFirstname());
    }

    public function testUpdatedLastNameIsSavedAndReturned()
    {
        $author = new Author($this->id);
        $author->setLastname($this->validLname);
        unset($author);

        $author2 = new Author($this->id);
        $this->assertEquals($this->validLname, $author2->getLastname());
    }

    public function testUpdatedFirstAndLastNameIsSavedAndReturned()
    {
        $author = new Author($this->id);
        $author->setFirstName($this->validFname);
        $author->setLastname($this->validLname);
        unset($author);

        $author2 = new Author($this->id);
        $this->assertEquals($this->validLname . ', ' . $this->validFname, $author2->getAuthorName());
    }

    public function testGetIdOfExistingAuthor()
    {
        $author = new Author($this->id);
        $this->assertEquals($this->id, $author->getId());
    }

    public function testReturnIdOfNewlyCreatedAuthor()
    {
        $author = new Author(null, $this->validFname, $this->validLname);
        $sql = 'select id from author where firstName = :fname and lastName = :lname';
        $this->assertEquals(
            $this->db->run($sql, ['fname' => $this->validFname, 'lname' => $this->validLname])->fetchColumn(),
            $author->getId()
        );
    }

    public function testGetExistingIdIfSavingDuplicate()
    {
        $author = new Author(null, $this->fname, $this->lname);
        $this->assertEquals($this->id, $author->getId());
    }

    public function testNewAuthorImmediatelyUpdatedIsSavedProperly()
    {
        $author = new Author(null, $this->validFname, $this->validLname);
        $id = $author->getId();

        $author->setFirstname($this->validFname2);
        unset($author);

        $author2 = new Author($id);
        $this->assertEquals($this->validFname2, $author2->getFirstname());
    }

    public function testFirstNameIsUpdatedForExistingAuthorWhenPassedInConstructor()
    {
        $author = new Author($this->id, $this->validFname);
        unset($author);
        $authorUpdated = new Author($this->id);
        $this->assertEquals($this->validFname, $authorUpdated->getFirstname());
    }

    public function testLastNameIsUpdatedForExistingAuthorWhenPassedInConstructor()
    {
        $author = new Author($this->id, null, $this->validLname);
        unset($author);
        $authorUpdated = new Author($this->id);
        $this->assertEquals($this->validLname, $authorUpdated->getLastname());
    }

    public function testThrowOutOfRangeExceptionWhenAskingFOrAuthorWithIncorrectId()
    {
        $this->expectException('OutOfRangeException');
        $author = new Author(-1);
        $author->getId();
    }

    public function tesAuthorNewFirstNameIsNotOverwrittenAccidentallyByLoadingDBParams()
    {
        $author = new Author($this->id, $this->validFname);
        $author->getId();
        $this->assertTrue($this->validFname == $author->getFirstname());
    }

    public function tearDown(): void
    {
        $sql = '
            delete from author
            where id = :id or firstName = :vf1 or firstName = :vf2 or lastName = :vl1 or lastName = :vl2
        ';
        $this->db->run($sql, [
            'id' => $this->id,
            'vf1' => $this->validFname,
            'vf2' => $this->validFname2,
            'vl1' => $this->validLname,
            'vl2' => $this->validLname2
        ]);
        unset($this->id);
        unset($this->fname);
        unset($this->lname);
    }
}
