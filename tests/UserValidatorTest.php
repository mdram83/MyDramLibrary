<?php

use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Validator\UserValidator;

class UserValidatorTest extends TestCase {

    public function testIsValidUserNameReturnsTrueForPermittedChars() {
        $this->assertTrue(UserValidator::isValidUsername('1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM'));
    }

    public function testIsValidUserNameReturnsFalseForSpecialChar() {
        $this->assertFalse(UserValidator::isValidUsername('.'));
    }

    public function testIsValidUserNameReturnsFalseForLocalChar() {
        $this->assertFalse(UserValidator::isValidUsername('ą'));
    }

    public function testIsValidUserNameReturnsFalseForNL() {
        $this->assertFalse(UserValidator::isValidUsername("\n"));
    }

    public function testIsValidUserNameReturnsFalseForTag() {
        $this->assertFalse(UserValidator::isValidUsername('<p>'));
    }

    public function testIsValidUserNameReturnsFalseForEmpty() {
        $this->assertFalse(UserValidator::isValidUsername(''));
    }

    public function testIsValidUserNameReturnsFalseForLongerThan100() {
        $name = '';
        for ($i = 1; $i <= 101; $i++) {
            $name .= 'a';
        }
        $this->assertFalse(UserValidator::isValidUsername($name));
    }

    public function testIsValidUserNameReturnsTrueForLongerMax100Chars() {
        $name = '';
        for ($i = 1; $i <= 100; $i++) {
            $name .= 'a';
        }
        $this->assertTrue(UserValidator::isValidUsername($name));
    }

    public function testIsValidUserNameReturnsTrueForMin1Char() {

        $this->assertTrue(UserValidator::isValidUsername('a'));
    }

    public function testIsValidEmailReturnsTrueForValidEmailNameWithDomainCom() {
        $this->assertTrue(UserValidator::isValidEmail('example@email.com'));
    }

    public function testIsValidEmailReturnsFalseForValidEmailNameWithDomain() {
        $this->assertFalse(UserValidator::isValidEmail('example@email'));
    }

    public function testIsValidEmailReturnsTrueForValidEmailDoubleNameWithDomainCom() {
        $this->assertTrue(UserValidator::isValidEmail('example.blabla@email.com'));
    }

    public function testIsValidEmailReturnsFalseMissingAt() {
        $this->assertFalse(UserValidator::isValidEmail('example.email.com'));
    }

    public function testIsValidEmailReturnsFalseMissingName() {
        $this->assertFalse(UserValidator::isValidEmail('@email.com'));
    }

    public function testIsValidPasswordTrueForAllRequiredCharsAndLengthBetween8and72() {
        $partUppercase  = 'QWERTYQWERTYQWERTY';
        $partLowercase  = 'qwertyqwertyqwerty';
        $partDigits     = '123456789123456789';
        $partSpecial    = '!@#$%^&*(!@#$%^&*(';
        $password = $partUppercase.$partLowercase.$partDigits.$partSpecial;
        $this->assertTrue(UserValidator::isValidPassword($password));
    }

    public function testIsValidPasswordFalseMissingUppercase() {
        $partUppercase  = '';
        $partLowercase  = 'qwertyqwertyqwerty';
        $partDigits     = '123456789123456789';
        $partSpecial    = '!@#$%^&*(!@#$%^&*(';
        $password = $partUppercase.$partLowercase.$partDigits.$partSpecial;
        $this->assertFalse(UserValidator::isValidPassword($password));
    }

    public function testIsValidPasswordFalseMissingLowercase() {
        $partUppercase  = 'QWERTYQWERTYQWERTY';
        $partLowercase  = '';
        $partDigits     = '123456789123456789';
        $partSpecial    = '!@#$%^&*(!@#$%^&*(';
        $password = $partUppercase.$partLowercase.$partDigits.$partSpecial;
        $this->assertFalse(UserValidator::isValidPassword($password));
    }

    public function testIsValidPasswordFalseMissingDigit() {
        $partUppercase  = 'QWERTYQWERTYQWERTY';
        $partLowercase  = 'qwertyqwertyqwerty';
        $partDigits     = '';
        $partSpecial    = '!@#$%^&*(!@#$%^&*(';
        $password = $partUppercase.$partLowercase.$partDigits.$partSpecial;
        $this->assertFalse(UserValidator::isValidPassword($password));
    }

    public function testIsValidPasswordFalseMissingSpecialChar() {
        $partUppercase  = 'QWERTYQWERTYQWERTY';
        $partLowercase  = 'qwertyqwertyqwerty';
        $partDigits     = '123456789123456789';
        $partSpecial    = '';
        $password = $partUppercase.$partLowercase.$partDigits.$partSpecial;
        $this->assertFalse(UserValidator::isValidPassword($password));
    }

    public function testIsValidPasswordFalseTooShort7Chars() {
        $this->assertFalse(UserValidator::isValidPassword('qQ8&qQ8'));
    }

    public function testIsValidPasswordFalseTooLong73Chars() {
        $partUppercase  = 'QWERTYQWERTYQWERTY';
        $partLowercase  = 'qwertyqwertyqwerty';
        $partDigits     = '123456789123456789';
        $partSpecial    = '!@#$%^&*(!@#$%^&*(';
        $password = $partUppercase.$partLowercase.$partDigits.$partSpecial.'a';
        $this->assertFalse(UserValidator::isValidPassword($password));
    }

}