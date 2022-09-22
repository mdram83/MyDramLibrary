<?php

namespace MyDramLibrary\User;

use Exception;
use MyDramLibrary\Utilities\Database\Database;
use MyDramLibrary\Utilities\Validator\UserValidator;

class User
{
    private ?int $userId = null;

    private ?string $username = null;
    private ?string $email = null;
    private ?string $password = null;
    private ?string $verificationHash = null;
    private bool $verified;
    private bool $active;

    private Database $database;
    private bool $loaded = false;
    private bool $updated = false;

    public function __construct(?int $userId = null)
    {
        if ($userId !== null) {
            $this->userId = $userId;
            $this->database = Database::instance();
            $this->loadUser();
        }
    }

    public function __destruct()
    {
        if ($this->loaded && $this->updated) {
            $this->update();
        }
    }

    public function getUserId(): int
    {
        if ($this->userId) {
            return $this->userId;
        } else {
            throw new Exception('UserId not set');
        }
    }

    private function loadUser(): void
    {
        if (!$this->loaded) {
            $sql = 'select username, email, password, verification_hash, verified, active from user where id = :id';
            $id = $this->getUserId();
            if ($data = $this->database->run($sql, [':id' => $id])->fetch()) {
                $this->username = $data['username'];
                $this->email = $data['email'];
                $this->password = $data['password'];
                $this->verificationHash = $data['verification_hash'];
                $this->verified = $data['verified'];
                $this->active = $data['active'];
                $this->loaded = true;
            } else {
                throw new Exception("User not found");
            }
        }
    }

    private function update(): void
    {
        if ($this->updated) {
            $data = [
                'id'                => $this->getUserId(),
                'username'          => $this->getUsername(),
                'email'             => $this->getEmail(),
                'password'          => $this->getPassword(),
                'verification_hash' => $this->verificationHash,
                'verified'          => (int) $this->isVerified(),
                'active'            => (int) $this->isActive(),
            ];
            $sql = '
                update user set
                    username = :username,
                    email = :email,
                    password = :password,
                    verification_hash = :verification_hash,
                    verified = :verified,
                    active = :active
                where id = :id';
            $this->database->run($sql, $data);
            $this->updated = false;
        }
    }

    public function registerUser(): string
    {
        if ($this->userId) {
            throw new Exception('User already registered');
        }
        if (!$this->username) {
            throw new Exception('Username missing');
        }
        if (!$this->email) {
            throw new Exception('Email missing');
        }
        if (!$this->password) {
            throw new Exception('Password missing');
        }

        $this->database = Database::instance();
        $this->generateVerificationHash();

        $sql = '
            insert into user (
                username,
                email,
                password,
                verification_hash,
                verified,
                active,
                created
            ) values (
                :username,
                :email,
                :password,
                :verification_hash,
                0,
                1,
                now()
            )';
        $data = [
            'username'          => $this->username,
            'email'             => $this->email,
            'password'          => $this->password,
            'verification_hash' => $this->verificationHash,
        ];
        $this->database->run($sql, $data);
        $this->userId = $this->database->lastInsertId();

        $this->verified = 0;
        $this->active = 1;

        $this->updated = false;
        $this->loaded = true;

        return $this->verificationHash;
    }

    public function isVerified(): bool
    {
        $this->loadUser();
        return $this->verified;
    }

    public function isActive(): bool
    {
        $this->loadUser();
        return $this->active;
    }

    public function verify(string $verificationHash): bool
    {
        if (!$this->loaded) {
            throw new Exception('User not registered');
        }
        if ($this->isVerified()) {
            throw new Exception('User verified');
        }
        if (!$this->isActive()) {
            throw new Exception('User inactive');
        }

        if ($this->verificationHash != $verificationHash) {
            return false;
        } else {
            $this->verified = true;
            $this->updated = true;
            return true;
        }
    }

    public function deactivate(): void
    {
        if (!$this->loaded) {
            throw new Exception('User not registered');
        }
        if (!$this->isActive()) {
            throw new Exception('User inactive');
        }
        $this->active = false;
        $this->updated = true;
    }

    public function getUsername(): string
    {
        $this->loadUser();
        return $this->username;
    }

    public function getEmail(): string
    {
        $this->loadUser();
        return $this->email;
    }

    private function getPassword(): string
    {
        $this->loadUser();
        return $this->password;
    }

    public function setUsername(string $username): void
    {
        if ($this->isValidUsername($username)) {
            $this->username = $username;
            $this->updated = true;
        } else {
            throw new Exception('Invalid username format');
        }
    }

    public function setEMail(string $email): void
    {
        if ($this->isValidEmail($email)) {
            $this->email = $email;
            $this->updated = true;
        } else {
            throw new Exception('Invalid email address format');
        }
    }

    public function setPassword(string $password): void
    {
        if ($this->isValidPassword($password)) {
            $this->password = $this->hashPassword($password);
            $this->updated = true;
        } else {
            throw new Exception('Invalid password format');
        }
    }

    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->getPassword());
    }

    private function isValidUsername(string $username): bool
    {
        return UserValidator::isValidUsername($username);
    }

    private function isValidEmail(string $email): bool
    {
        return UserValidator::isValidEmail($email);
    }

    private function isValidPassword(string $password): bool
    {
        return UserValidator::isValidPassword($password);
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function generateVerificationHash(): void
    {
        $this->username ?? throw new Exception('Can\'t create hash for user.');
        $this->verificationHash = password_hash($this->username . time(), PASSWORD_BCRYPT);
    }
}
