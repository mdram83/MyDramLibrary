<?php

namespace MyDramLibrary\Utilities\Validator;

class CatalogValidator extends Validator
{
    private static function isSingleLineString(string $string): bool
    {
        return (!self::hasForbiddenCharacters($string, "\n\t\r"));
    }

    public static function isValidPublisherName(string $name): bool
    {
        return (preg_match('/^([\p{L} \d\D\w\W]){1,255}$/ui', $name) && self::isSingleLineString($name));
    }

    public static function isValidCategoryName(string $name): bool
    {
        return (preg_match('/^([\p{L}\d \.&,-]){1,100}$/ui', $name) && self::isSingleLineString($name));
    }

    public static function isValidAuthorName(string $name): bool
    {
        return (self::isStringLengthBetween($name, 1, 255) && self::isSingleLineString($name));
    }

    public static function isValidTitle(string $name): bool
    {
        return (preg_match('/^([\p{L} \d\D\w\W]){1,500}$/ui', $name) && self::isSingleLineString($name));
    }

    public static function isValidISBN(string $isbn): bool
    {
        $isbn = str_replace('-', '', $isbn);
        if (!(preg_match('/^(\d{9}(\d|X))$/', $isbn) || preg_match('/^(\d){13}$/', $isbn))) {
            return false;
        }
        return self::{'checkSumISBN' . strlen($isbn)}($isbn);
    }

    private static function checkSumISBN10(string $isbn): bool
    {
        $stringArray = str_split($isbn);
        for ($i = 1, $sum = 0; $i < 10; $i++) {
            $sum += (int) $stringArray[$i - 1] * $i;
        }
        return ($sum % 11 == str_replace('X', '10', $stringArray[9]));
    }

    private static function checkSumISBN13(string $isbn): bool
    {
        $stringArray = str_split($isbn);
        for ($i = 1, $sum = 0; $i < 13; $i++) {
            $sum += (int) $stringArray[$i - 1] * (($i % 2 == 0) ? 3 : 1);
        }
        return ((($sum % 10 == 0) ? 0 : (10 - $sum % 10)) == $stringArray[12]);
    }

    public static function isValidSeries(string $name): bool
    {
        return (preg_match('/^([\p{L}\d \'\"\.&,-]){1,100}$/ui', $name) && self::isSingleLineString($name));
    }

    public static function isValidVolume(int $volume): bool
    {
        return self::isNumberBetween($volume, 1, 65535);
    }

    public static function isValidPages(int $pages): bool
    {
        return self::isNumberBetween($pages, 1, 65535);
    }

    public static function isValidDescription(string $description): bool
    {
        return self::isStringLengthBetween($description, 1, 10000);
    }

    public static function isValidComment(string $comment): bool
    {
        return self::isStringLengthBetween($comment, 1, 10000);
    }
}
