<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use MyDramLibrary\User\User;
use MyDramLibrary\Utilities\Database\Database;

class UserTest extends TestCase
{
    private Database $database;

    private int $existingUserId;
    private string $existingUsername;
    private string $existingEmail;

    private string $existingUsername2;
    private string $existingEmail2;

    final public function setUp(): void
    {
        $this->database = Database::instance();

        // First Test User
        $username = 'UserTest01Time' . time();
        $email = $username . '@UserTest.com';
        $this->existingUserId = $this->addTestUserToDatabase($username, $email);

        $sql = 'select username from user where id = :id';
        $this->existingUsername = $this->database->run($sql, ['id' => $this->existingUserId])->fetchColumn();

        $sql = 'select email from user where id = :id';
        $this->existingEmail = $this->database->run($sql, ['id' => $this->existingUserId])->fetchColumn();

        // Second Test User
        $username = 'UserTest02Time' . time();
        $email = $username . '@UserTest.com';
        $existingUserId2 = $this->addTestUserToDatabase($username, $email);

        $sql = 'select username from user where id = :id';
        $this->existingUsername2 = $this->database->run($sql, ['id' => $existingUserId2])->fetchColumn();

        $sql = 'select email from user where id = :id';
        $this->existingEmail2 = $this->database->run($sql, ['id' => $existingUserId2])->fetchColumn();
    }

    private function addTestUserToDatabase(string $username, string $email)
    {
        $this->database = Database::instance();
        $sql = '
        insert into user
            (username, email, password, verification_hash, verified, active, created)
        values
            (:username, :email, :password, :verification_hash, 0, 0, now())';
        $args = [
            'username' => $username,
            'email' => $email,
            'password' => 'dummy',
            'verification_hash' => 'dummy',
        ];
        $this->database->run($sql, $args);
        return $this->database->lastInsertId();
    }

    public function testUserObjectCreatedWithoutId()
    {
        $this->assertInstanceOf('MyDramLibrary\User\User', new User());
    }

    public function testUserObjectCreatedWithId()
    {
        $this->assertInstanceOf('MyDramLibrary\User\User', new User($this->existingUserId));
    }

    public function testReturnUserIdForUserWithId()
    {
        $user = new User($this->existingUserId);
        $this->assertEquals($this->existingUserId, $user->getUserId());
    }

    public function testThrowExceptionIfRequestedForIdForUserWithoutId()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->getUserId();
    }

    public function testThrowExceptionIfUserIsMissingOrWrongId()
    {
        $this->expectException('Exception');
        new User(0);
    }

    public function testReturnUsernameForUserWithId()
    {
        $user = new User($this->existingUserId);
        $this->assertEquals($this->existingUsername, $user->getUsername());
    }

    public function testReturnEmailForUserWithId()
    {
        $user = new User($this->existingUserId);
        $this->assertEquals($this->existingEmail, $user->getEmail());
    }

    public function testThrowExceptionIfRequestedForUsernameForUserWithoutId()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->getUsername();
    }

    public function testThrowExceptionIfRequestedForEmailForUserWithoutId()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->getEmail();
    }

    public function testUpdateAndReturnUpdatedUserName()
    {
        $user1 = new User($this->existingUserId);
        $user1->setUsername('test2id' . time());
        $newUsername1 = $user1->getUsername();
        unset($user1);
        $user2 = new User($this->existingUserId);
        $this->assertEquals($user2->getUsername(), $newUsername1);
    }

    public function testUpdateUserEmailToAlreadyExistingForDifferentUser()
    {
        $this->expectException('PDOException');
        $user = new User($this->existingUserId);
        $user->setEmail($this->existingEmail2);
    }

    public function testThrowExceptionIfRegisteringUserWithId()
    {
        $this->expectException('Exception');
        $user = new User(1);
        $user->registerUser();
    }

    public function testThrowExceptionIfRegisteringUserWithoutUserName()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setEmail('test' . time() . '@UserTest.com');
        $user->setPassword('pwd');
        $user->registerUser();
    }

    public function testThrowExceptionIfRegisteringUserWithoutEmail()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setUsername('test' . time());
        $user->setPassword('pwd');
        $user->registerUser();
    }

    public function testThrowExceptionIfRegisteringUserWithoutPassword()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setUsername('test' . time());
        $user->setEmail('test' . time() . '@UserTest.com');
        $user->registerUser();
    }

    public function testUsernameIsValid()
    {
        $user = new User();
        $user->setUsername('test' . time());
        $this->addToAssertionCount(1);
    }

    public function testUsernameIsInvalid()
    {
        $this->expectException('Exception');
        $user = new User();
        $invalidUsername = '
            1234567890abcdefghij
            1234567890abcdefghij
            1234567890abcdefghij
            1234567890abcdefghij
            1234567890abcdefghij
            1234567890abcdefghij
        ';
        $user->setUsername($invalidUsername);
    }

    public function testEmailIsValid()
    {
        $user = new User();
        $user->setEmail('valid.email@atdomain.com');
        $this->addToAssertionCount(1);
    }

    public function testEmailIsInvalid()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setEmail('invalid@email.');
    }

    public function testPasswordIsValid()
    {
        $user = new User();
        $user->setPassword('V@l1dP@$$w0Rd');
        $this->addToAssertionCount(1);
    }

    public function testPasswordIsInvalidMissSpecialChar()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setPassword('Inval1dpassword');
    }

    public function testPasswordIsInvalidMissUpperCase()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setPassword('inv@l1dpassword');
    }

    public function testPasswordIsInvalidMissLowerCase()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setPassword('INV@L1DPWD');
    }

    public function testPasswordIsInvalidMissNumber()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setPassword('Invalidp@ssword');
    }

    public function testThrowExceptionIfRegisteringUserWithExistingUsername()
    {
        $this->expectException('PDOException');
        $user = new User();
        $user->setUsername($this->existingUsername);
        $user->setEmail('costam.costam@UserTest.com');
        $user->setPassword('jakiesH@as1o');
        $user->registerUser();
    }

    public function testThrowExceptionIfRegisteringUserWithExistingEmail()
    {
        $this->expectException('PDOException');
        $user = new User();
        $user->setUsername('dupek' . time());
        $user->setEmail($this->existingEmail);
        $user->setPassword('jakiesH@as1o');
        $user->registerUser();
    }

    public function testReturnsIdForRegisteredUser()
    {
        $user = new User();
        $user->setUsername('test1' . time());
        $user->setEmail('test1' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $user->registerUser();
        $this->assertIsInt($user->getUserId());
    }

    public function testRegisteredUserIsNotVerified()
    {
        $user = new User();
        $user->setUsername('test2' . time());
        $user->setEmail('test2' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $user->registerUser();
        $this->assertFalse($user->isVerified());
    }

    public function testRegisteredUserIsActive()
    {
        $user = new User();
        $user->setUsername('test3' . time());
        $user->setEmail('test3' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $user->registerUser();
        $this->assertTrue($user->isActive());
    }

    public function testThrowExceptionWhenVerifyingNotRegisteredUser()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setUsername('test4' . time());
        $user->setEmail('test4' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $user->verify('any_hash');
    }

    public function testReturnFalseIfVerifyingUserWithWrongHash()
    {
        $user = new User();
        $user->setUsername('test5' . time());
        $user->setEmail('test5' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $user->registerUser();
        $this->assertFalse($user->verify('wrong_hash'));
    }

    public function testReturnVerificationHashAfterRegisteringUser()
    {
        $user = new User();
        $user->setUsername('test6' . time());
        $user->setEmail('test6' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $this->assertIsString($user->registerUser());
    }

    public function testReturnTrueIfVerifyingUserWithCorrectHash()
    {
        $user = new User();
        $user->setUsername('test7' . time());
        $user->setEmail('test7' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $hash = $user->registerUser();
        $this->assertTrue($user->verify($hash));
    }

    public function testThrowExceptionWhenVerifyingVerifiedUser()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setUsername('test8' . time());
        $user->setEmail('test8' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $hash = $user->registerUser();
        $userId = $user->getUserId();
        $user->verify($hash);
        unset($user);

        $user2 = new User($userId);
        $user2->verify($hash);
    }

    public function testThrowExceptionWhenVerifyingInactiveUser()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->setUsername('test9' . time());
        $user->setEmail('test9' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $hash = $user->registerUser();
        $userId = $user->getUserId();
        $user->deactivate();
        unset($user);

        $user2 = new User($userId);
        $user2->verify($hash);
    }

    public function testNewUserIsCreatedVerifiedAndInformationSaved()
    {
        $user = new User();
        $user->setUsername('test10' . time());
        $user->setEmail('test10' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $user->verify($user->registerUser());
        $userId = $user->getUserId();
        unset($user);

        $user2 = new User($userId);
        $this->assertTrue($user2->isVerified());
    }

    public function testNewUserIsDeactivatedAndUpdated()
    {
        $user = new User();
        $user->setUsername('test11' . time());
        $user->setEmail('test11' . time() . '@UserTest.com');
        $user->setPassword('Dup@c1pa');
        $user->verify($user->registerUser());
        $user->deactivate();
        $userId = $user->getUserId();
        unset($user);

        $user2 = new User($userId);
        $this->assertFalse($user2->isActive());
    }

    public function testThrowExceptionIfCheckingPasswordForNotLoadedUser()
    {
        $this->expectException('Exception');
        $user = new User();
        $user->checkPassword('dummy');
    }

    public function testReturnFalseForIncorrectPassword()
    {
        $password = 'Dup@c1pa';
        $user = new User($this->existingUserId);
        $user->setPassword($password);
        unset($user);

        $user2 = new User($this->existingUserId);
        $this->assertFalse($user2->checkPassword($password . time()));
    }

    public function testReturnTrueForCorrectPassword()
    {
        $password = 'Dup@c1pa';
        $user = new User($this->existingUserId);
        $user->setPassword($password);
        unset($user);

        $user2 = new User($this->existingUserId);
        $this->assertTrue($user2->checkPassword($password));
    }

    final public function tearDown(): void
    {
        $this->database = Database::instance();
        $sql = 'delete from user where email like \'%@UserTest.com\'';
        $this->database->run($sql);
    }
}
