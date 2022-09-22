<?php

namespace MyDramLibrary\User;

class UserSession
{
    protected static ?UserSession $instance = null;

    private $sessionId;
    private bool $loggedIn;
    private ?int $userId;
    private ?string $username;

    private function __construct()
    {
        session_start();
        $this->loggedIn = $_SESSION['loggedInToLibrary'] ?? false;
        $this->userId = $_SESSION['userId'] ?? null;
        $this->username = $_SESSION['username'] ?? null;
    }

    public static function instance(): UserSession
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function isLoggedIn(): bool
    {
        return $this->loggedIn;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
