<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use MyDramLibrary\User\User;
use MyDramLibrary\User\UserLogin;
use MyDramLibrary\Utilities\Database\Database;

class UserLoginTest extends TestCase
{
    private Database $database;
    private UserLogin $userLogin;

    final public function setUp(): void
    {
        $this->database = Database::instance();
        $this->userLogin = new UserLogin();
        unset($_SESSION['loggedInToLibrary']);
        unset($_SESSION['username']);
        unset($_SESSION['userUd']);
    }

    private function addTestUser(string $username, string $email, string $password): int
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->verify($user->registerUser());
        return $user->getUserId();
    }

    public function testUserLoginProperObjectCreated()
    {
        $this->assertInstanceOf('MyDramLibrary\User\UserLogin', $this->userLogin);
    }

    public function testThrowExceptionWhenOverwritingUserSearchString()
    {
        $this->expectException('Exception');
        $this->userLogin->setUserSearchString('blabla@bla.bla');
        $this->userLogin->setUserSearchString('blabla@bla.bla');
    }

    public function testThrowExceptionWhenSettingUserSearchStringWhenIdIsSet()
    {
        $this->expectException('Exception');
        $this->userLogin->setUserSearchId(1);
        $this->userLogin->setUserSearchString('costam');
    }

    public function testAllowSetSearchStringFirstTime()
    {
        $this->userLogin->setUserSearchString('costam');
        $this->addToAssertionCount(1);
    }

    public function testThrowExceptionWhenOverwritingUserSearchId()
    {
        $this->expectException('Exception');
        $this->userLogin->setUserSearchId(1);
        $this->userLogin->setUserSearchId(2);
    }

    public function testThrowExceptionWhenSettingUserSearchIdWhenStringIsSet()
    {
        $this->expectException('Exception');
        $this->userLogin->setUserSearchString('costam');
        $this->userLogin->setUserSearchId(1);
    }

    public function testAllowSetSearchIdFirstTime()
    {
        $this->userLogin->setUserSearchId(1);
        $this->addToAssertionCount(1);
    }

    public function testReturnFalseAskingForLoggedInUserIfNotLoggedIn()
    {
        $this->assertFalse($this->userLogin->isLoggedIn());
    }

    public function testReturnTrueAskingForLoggedInUserIfLoggedIn()
    {
        $_SESSION['loggedInToLibrary'] = true;
        $newUserLogin = new UserLogin();
        $this->assertTrue($newUserLogin->isLoggedIn());
    }

    public function testThrowExceptionIfSettingSearchIdForLoggedUser()
    {
        $this->expectException('Exception');
        $_SESSION['loggedInToLibrary'] = true;
        $newUserLogin = new UserLogin();
        $newUserLogin->setUserSearchId(1);
    }

    public function testThrowExceptionIfSettingSearchStringForLoggedUser()
    {
        $this->expectException('Exception');
        $_SESSION['loggedInToLibrary'] = true;
        $newUserLogin = new UserLogin();
        $newUserLogin->setUserSearchString('costam');
    }

    public function testReturnFalseWhenLoggingLoggedUser()
    {
        $_SESSION['loggedInToLibrary'] = true;
        $username = 'UserLoginTest01';
        $email = 'UserLoginTest01@UserTest.com';
        $password = 'Dup@c1pa';
        $this->addTestUser($username, $email, $password);

        $newUserLogin = new UserLogin();
        $this->assertFalse($newUserLogin->logInWithEmail($email, $password));
    }

    public function testReturnFalseLoggingNotExistingUserEmail()
    {
        $userLogin = new UserLogin();
        $this->assertFalse($userLogin->logInWithEmail('wrongemail' . time(), 'w r ongpassword' . time()));
    }

    public function testReturnFalseLoggingValidUserWrongPassword()
    {
        $username = 'UserLoginTest03';
        $email = 'UserLoginTest03@UserTest.com';
        $password = 'Dup@c1pa';
        $this->addTestUser($username, $email, $password);

        $newUserLogin = new UserLogin();
        $this->assertFalse($newUserLogin->logInWithEmail($email, 'wrong_password'));
    }

    public function testReturnTrueLoggingValidNotLoggedUser()
    {
        $username = 'UserLoginTest04';
        $email = 'UserLoginTest04@UserTest.com';
        $password = 'Dup@c1pa';
        $this->addTestUser($username, $email, $password);

        $newUserLogin = new UserLogin();
        $this->assertTrue($newUserLogin->logInWithEmail($email, $password));
    }

    public function testSessionVariableAndParamLoggedInSetToTrueAfterCorrectLogin()
    {
        $username = 'UserLoginTest05';
        $email = 'UserLoginTest05@UserTest.com';
        $password = 'Dup@c1pa';
        $this->addTestUser($username, $email, $password);

        $newUserLogin = new UserLogin();
        $newUserLogin->logInWithEmail($email, $password);
        $this->assertTrue($_SESSION['loggedInToLibrary'] && $newUserLogin->isLoggedIn());
    }

    public function testSessionVariableUserIdSetAfterCorrectLogin()
    {
        $username = 'UserLoginTest06';
        $email = 'UserLoginTest06@UserTest.com';
        $password = 'Dup@c1pa';
        $userId = $this->addTestUser($username, $email, $password);

        $userLogin = new UserLogin();
        $userLogin->logInWithEmail($email, $password);
        $this->assertEquals($_SESSION['userId'], $userId);
    }

    public function testSessionVariableUsernameSetAfterCorrectLogin()
    {
        $username = 'UserLoginTest07';
        $email = 'UserLoginTest07@UserTest.com';
        $password = 'Dup@c1pa';
        $this->addTestUser($username, $email, $password);

        $userLogin = new UserLogin();
        $userLogin->logInWithEmail($email, $password);
        $this->assertEquals($_SESSION['username'], $username);
    }

    public function testSessionVariableAndParamLoggedInSetToFalseAfterIncorrectLogin()
    {
        $username = 'UserLoginTest08';
        $email = 'UserLoginTest08@UserTest.com';
        $password = 'Dup@c1pa';
        $this->addTestUser($username, $email, $password);

        $newUserLogin = new UserLogin();
        $newUserLogin->logInWithEmail($email, 'wrong_password');
        $this->assertFalse($_SESSION['loggedInToLibrary'] || $newUserLogin->isLoggedIn());
    }

    public function testSessionVariableUserIdAndUsernameUnsetAfterIncorrectLogin()
    {
        $username = 'UserLoginTest09';
        $email = 'UserLoginTest09@UserTest.com';
        $password = 'Dup@c1pa';
        $this->addTestUser($username, $email, $password);

        $_SESSION['userId'] = 1;
        $_SESSION['username'] = 'something';

        $userLogin = new UserLogin();
        $userLogin->logInWithEmail($email, 'wrong_password');
        $this->assertFalse(isset($_SESSION['userId']) || isset($_SESSION['username']));
    }

    public function testSessionAndParamLoggedInSetToFalseWhenLoggingLoggedUser()
    {
        $_SESSION['loggedInToLibrary'] = true;
        $username = 'UserLoginTest10';
        $email = 'UserLoginTest10@UserTest.com';
        $password = 'Dup@c1pa';
        $this->addTestUser($username, $email, $password);

        $newUserLogin = new UserLogin();
        $newUserLogin->logInWithEmail($email, $password);
        $this->assertFalse($newUserLogin->isLoggedIn() || $_SESSION['loggedInToLibrary']);
    }

    public function testThrowLoginUnverifiedUserWhenLoggingUserNotYetVerified()
    {
        $this->expectException('MyDramLibrary\CustomException\LoginUnverifiedUserException');

        $username = 'UserLoginTest11';
        $email = 'UserLoginTest11@UserTest.com';
        $password = 'Dup@c1pa';

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->registerUser();

        $newUserLogin = new UserLogin();
        $newUserLogin->logInWithEmail($email, $password);
    }

    public function testThrowLoginInactiveUserWhenLoggingInactiveUser()
    {
        $this->expectException('MyDramLibrary\CustomException\LoginInactiveUserException');

        $username = 'UserLoginTest12';
        $email = 'UserLoginTest12@UserTest.com';
        $password = 'Dup@c1pa';

        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->verify($user->registerUser());
        $user->deactivate();
        unset($user);

        $newUserLogin = new UserLogin();
        $newUserLogin->logInWithEmail($email, $password);
    }

    final public function tearDown(): void
    {
        unset($this->userLogin);
        $this->database = Database::instance();
        $sql = 'delete from user where email like \'%@UserTest.com\'';
        $this->database->run($sql);
    }
}
