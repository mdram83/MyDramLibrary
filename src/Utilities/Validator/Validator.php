<?php

namespace MyDramLibrary\Utilities\Validator;

class Validator
{
    public static function isNumberBetween(float $number, float $from, float $to): bool
    {
        return ($number >= $from && $number <= $to);
    }

    public static function isStringLengthBetween(string $string, int $from, int $to): bool
    {
        $length = strlen($string);
        return ($length >= $from && $length <= $to);
    }

    public static function hasForbiddenCharacters(string $testedString, string $forbiddenChars): bool
    {
        for ($i = 0; $i <= strlen($testedString) - 1; $i++) {
            $char = substr($testedString, $i, 1);
            if (!(strpos($forbiddenChars, $char) === false)) {
                return true;
            }
        }
        return false;
    }
}
