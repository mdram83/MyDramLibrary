<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use MyDramLibrary\User\UserLookup;
use MyDramLibrary\Utilities\Database\Database;

class UserLookupTest extends TestCase
{
    private UserLookup $userLookup;

    private int $existingUser1Id;
    private string $existingUser1Username;
    private string $existingUser1Email;

    private int $existingUser2Id;
    private string $existingUser2Username;
    private string $existingUser2Email;

    final public function setUp(): void
    {
        $this->userLookup = new UserLookup();
        $database = Database::instance();

        $sql1 = 'select id, username, email from user order by id limit 1';
        if ($data1 = $database->run($sql1)->fetch()) {
            $this->existingUser1Id = $data1['id'];
            $this->existingUser1Username = $data1['username'];
            $this->existingUser1Email = $data1['email'];
        }

        $sql2 = 'select id, username, email from user order by id desc limit 1';
        if ($data2 = $database->run($sql2)->fetch()) {
            $this->existingUser2Id = $data2['id'];
            $this->existingUser2Username = $data2['username'];
            $this->existingUser2Email = $data2['email'];
        }
    }

    public function testUserLookupObjectCreated()
    {
        $this->assertInstanceOf('MyDramLibrary\User\UserLookup', $this->userLookup);
    }

    public function testSetLookupValue()
    {
        $lookupValue = 'testLookupValue';
        $this->userLookup->setLookupValue($lookupValue);
        $this->addToAssertionCount(1);
    }

    public function testGetLookupValue()
    {
        $lookupValue = 'testLookupValue';
        $this->userLookup->setLookupValue($lookupValue);
        $this->assertEquals($lookupValue, $this->userLookup->getLookupValue());
    }

    public function testThrowExceptionWhenGettingUnsetLookupValue()
    {
        $this->expectException('Exception');
        $this->userLookup->getLookupValue();
    }

    public function testReturnedLookupValueIsString()
    {
        $lookupValue = 123;
        $this->userLookup->setLookupValue($lookupValue);
        $this->assertIsString($this->userLookup->getLookupValue());
    }

    public function testThrowExceptionWhenGettingUserIdPriorToLoad()
    {
        $this->expectException('Exception');
        $this->userLookup->getUserId();
    }

    public function testGetExpectedUserIdWhenSearchedByUsername()
    {
        $this->userLookup->setLookupValue($this->existingUser1Username);
        $this->assertEquals($this->existingUser1Id, $this->userLookup->getUserId());
    }

    public function testGetExpectedUserIdWhenSearchedByEmail()
    {
        $this->userLookup->setLookupValue($this->existingUser2Email);
        $this->assertEquals($this->existingUser2Id, $this->userLookup->getUserId());
    }

    public function testGetZeroWhenSearchingForMissingUser()
    {
        $this->userLookup->setLookupValue('$$###   ');
        $this->assertSame(0, $this->userLookup->getUserId());
    }

    public function testResetUserIdAfterChangingLookupValue()
    {
        $this->userLookup->setLookupValue($this->existingUser1Username);
        $id1 = $this->userLookup->getUserId();
        $this->userLookup->setLookupValue($this->existingUser2Username);
        $id2 = $this->userLookup->getUserId();
        $this->assertFalse($id1 == $id2);
    }


    final public function tearDown(): void
    {
        unset($this->userLookup);
    }
}
