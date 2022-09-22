<?php

namespace MyDramLibrary\User;

use MyDramLibrary\CustomException\LoginInactiveUserException;
use MyDramLibrary\CustomException\LoginUnverifiedUserException;
use Exception;
use MyDramLibrary\User\User;

class UserLogin
{
    private UserLookup $userLookup;
    private string $userSearchString;
    private int $userSearchId;

    private UserSession $userSession;

    private bool $loggedIn;

    public function __construct()
    {
        $this->userSession = UserSession::instance();
        $this->loggedIn = $_SESSION['loggedInToLibrary'] ?? false;
    }

    public function setUserSearchId(int $id): void
    {
        if ($this->isAnySearchSet() || $this->isLoggedIn()) {
            throw new Exception('User parameter already set');
        }
        $this->userSearchId = $id;
    }

    public function setUserSearchString(string $searchString): void
    {
        if ($this->isAnySearchSet() || $this->isLoggedIn()) {
            throw new Exception('User parameter already set');
        }
        $this->userSearchString = $searchString;
    }

    private function isAnySearchSet(): bool
    {
        return (isset($this->userSearchId) || isset($this->userSearchString));
    }

    private function loadUserLookup(): void
    {
        if (!isset($this->userLookup)) {
            $this->userLookup = new UserLookup();
        }
    }

    public function isLoggedIn(): bool
    {
        return $this->loggedIn;
    }

    public function logInWithEmail(string $email, string $password): bool
    {
        if ($this->isLoggedIn()) {
            return $this->logOut();
        }
        $this->setUserSearchString($email);
        $this->loadUserLookup();
        $this->userLookup->setLookupValue($this->userSearchString);

        try {
            $user = new User($this->userLookup->getUserId());
        } catch (Exception $e) {
            return $this->logOut();
        }

        if (!$user->isVerified()) {
            throw new LoginUnverifiedUserException(
                'Before logging in please verify your account with link sent to your email'
            );
        }
        if (!$user->isActive()) {
            throw new LoginInactiveUserException('Account inactive');
        }

        if ($user->checkPassword($password)) {
            return $this->logIn($user->getUserId(), $user->getUsername());
        } else {
            return $this->logOut();
        }
    }

    private function logIn($userId, $username): bool
    {
        $this->loggedIn = true;
        $_SESSION['loggedInToLibrary'] = true;
        $_SESSION['userId'] = $userId;
        $_SESSION['username'] = $username;
        return true;
    }

    private function logOut(): bool
    {
        $this->loggedIn = false;
        $_SESSION['loggedInToLibrary'] = false;
        unset($_SESSION['userId']);
        unset($_SESSION['username']);
        return false;
    }
}
