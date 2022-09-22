<?php

use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Validator\Validator;

class ValidatorTest extends TestCase {

    public function testIsNumberBetweenReturnsTrueForPositiveNumbers() {
        $this->assertTrue(Validator::isNumberBetween(5, 1, 10));
    }

    public function testIsNumberBetweenReturnsTrueForNegativeNumbers() {
        $this->assertTrue(Validator::isNumberBetween(-5, -10, -1));
    }

    public function testIsNumberBetweenReturnsTrueForMixedNumbers() {
        $this->assertTrue(Validator::isNumberBetween(0, -10, 10));
    }

    public function testIsNumberBetweenReturnsTrueForBottomEdgeCase() {
        $this->assertTrue(Validator::isNumberBetween(-10.12345, -10.12345, 10.12345));
    }

    public function testIsNumberBetweenReturnsTrueForTopEdgeCase() {
        $this->assertTrue(Validator::isNumberBetween(10.12345, -10.12345, 10.12345));
    }

    public function testIsNumberBetweenReturnsFalseForExceedingBottom() {
        $this->assertFalse(Validator::isNumberBetween(-10.123451, -10.12345, 10.12345));
    }

    public function testIsNumberBetweenReturnsFalseForExceedingTop() {
        $this->assertFalse(Validator::isNumberBetween(10.123451, -10.12345, 10.12345));
    }

    public function testIsNumberBetweenThrowsErrorTypeExceptionIfCheckingString() {
        $this->expectException('\TypeError');
        Validator::isNumberBetween('a', -10.12345, 10.12345);
    }

    public function testIsNumberBetweenThrowsErrorTypeExceptionIfCheckingAgainstMinString() {
        $this->expectException('\TypeError');
        Validator::isNumberBetween(1, 'a', 10.12345);
    }

    public function testIsNumberBetweenThrowsErrorTypeExceptionIfCheckingAgainstMaxString() {
        $this->expectException('\TypeError');
        Validator::isNumberBetween(1, 0, 'max');
    }

    public function testIsStringLengthBetweenReturnTrueForCorrectCheck() {
        $this->assertTrue(Validator::isStringLengthBetween('test', 0, 10));
    }

    public function testIsStringLengthBetweenReturnTrueForCorrectCheckEdgeBottom() {
        $this->assertTrue(Validator::isStringLengthBetween('', 0, 10));
    }

    public function testIsStringLengthBetweenReturnTrueForCorrectCheckEdgeTop() {
        $this->assertTrue(Validator::isStringLengthBetween('1234567890', 0, 10));
    }

    public function testIsStringLengthBetweenReturnFalseForTooShortString() {
        $this->assertFalse(Validator::isStringLengthBetween('abc', 4, 10));
    }

    public function testIsStringLengthBetweenReturnFalseForTooLongString() {
        $this->assertFalse(Validator::isStringLengthBetween('12345678901', 4, 10));
    }

    public function testHasForbiddenCharactersReturnsTrueForMarchOneChar() {
        $this->assertTrue(Validator::hasForbiddenCharacters('tested string', 'i'));
    }

    public function testHasForbiddenCharactersReturnsTrueForMarchTwoChars() {
        $this->assertTrue(Validator::hasForbiddenCharacters('tested string', 'ai'));
    }

    public function testHasForbiddenCharactersReturnsTrueForMarchSpecialChars() {
        $this->assertTrue(Validator::hasForbiddenCharacters("tested string \n", "a\n"));
    }

    public function testHasForbiddenCharactersReturnsFalseForNoMatch() {
        $this->assertFalse(Validator::hasForbiddenCharacters("tested string", "a\n"));
    }
}
