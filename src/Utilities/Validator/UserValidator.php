<?php

namespace MyDramLibrary\Utilities\Validator;

class UserValidator extends Validator
{
    public static function isValidUsername(string $name): bool
    {
        return preg_match('/^([a-zA-Z0-9]){1,100}$/', $name);
    }

    public static function isValidEmail(string $email): bool
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
    }

    public static function isValidPassword(string $password): bool
    {
        return (
            preg_match('@[A-Z]@', $password) &&
            preg_match('@[a-z]@', $password) &&
            preg_match('@[0-9]@', $password) &&
            preg_match('@[^\w]@', $password) &&
            self::isStringLengthBetween($password, 8, 72)
        );
    }
}
