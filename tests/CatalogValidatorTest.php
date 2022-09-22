<?php

use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Validator\CatalogValidator;

class CatalogValidatorTest extends TestCase {

    public function testIsValidPublisherNameReturnsTrueForAnyCharsExceptBackSlashedNRT() {
        $name = '';
        for ($dec = 32; $dec <= 127; $dec++) {
            $name .= chr($dec);
        }
        $this->assertTrue(CatalogValidator::isValidPublisherName($name));
    }

    public function testIsValidPublisherNameReturnsFalseForNLChar() {
        $this->assertFalse(CatalogValidator::isValidPublisherName("Unsupported ".chr(10)));
    }

    public function testIsValidPublisherNameReturnFalseForBooleanFalsePublisherName() {
        $this->assertFalse(CatalogValidator::isValidPublisherName(false));
    }

    public function testIsValidPublisherNameReturnFalseForEmptyPublisherName() {
        $this->assertFalse(CatalogValidator::isValidPublisherName(''));
    }

    public function testIsValidPublisherNameReturnFalseForTooLongPublisherName() {
        $name = str_repeat('A', 255 + 1);
        $this->assertFalse(CatalogValidator::isValidPublisherName($name));
    }

    public function testIsValidCategoryNameReturnTrueForAllMatchingChars() {
        $this->assertTrue(CatalogValidator::isValidCategoryName("asdfghjklASDFGHJKLąśłĄŚŁ1234567890 -.&,"));
    }

    public function testIsValidCategoryNameReturnFalseForNameWithNewLine() {
        $this->assertFalse(CatalogValidator::isValidCategoryName("cośtam\n"));
    }

    public function testIsValidCategoryNameReturnFalseForEmptyCategoryName() {
        $this->assertFalse(CatalogValidator::isValidCategoryName(''));
    }

    public function testIsValidCategoryNameReturnFalseForTooLongCategoryName() {
        $name = str_repeat('A', 100 + 1);
        $this->assertFalse(CatalogValidator::isValidCategoryName($name));
    }

    public function testIsValidAuthorNameReturnTrueForAnyCharsExceptBackSlashedNRT() {
        $name = '';
        for ($dec = 32; $dec <= 127; $dec++) {
            $name .= chr($dec);
        }
        $this->assertTrue(CatalogValidator::isValidAuthorName($name));
    }

    public function testIsValidAuthorNameReturnFalseForEmptyName() {
        $this->assertFalse(CatalogValidator::isValidAuthorName(''));
    }

    public function testIsValidAuthorNameReturnFalseForNewLine() {
        $this->assertFalse(CatalogValidator::isValidAuthorName("cośtam\n"));
    }

    public function testIsValidAuthorNameReturnTrueForGermanCharacters() {
        $this->assertTrue(CatalogValidator::isValidAuthorName('Heinrich Böll'));
    }

    public function testIsValidAuthorNameReturnFalseForTooLongName() {
        $name = str_repeat('A', 255 + 1);
        $this->assertFalse(CatalogValidator::isValidAuthorName($name));
    }

    public function testIsValidTitleReturnsTrueForSpecialCharsSpacesTagsAndLocals() {
        $partNormalChars = 'ABCDEFGabcedg';
        $partWhiteSpace = ' ';
        $partSpecialChar = 'ążźćĄŚ構成員の固有';
        $partTags = '<html>';
        $partParenthesis = '\'\"';
        $title = $partNormalChars.$partWhiteSpace.$partSpecialChar.$partTags.$partParenthesis;
        $this->assertTrue(CatalogValidator::isValidTitle($title));
    }

    public function testIsValidTitleReturnsTrueFor500Chars() {
        $title = '';
        for ($i = 1; $i <= 500; $i++) {
            $title .= 'A';
        }
        $this->assertTrue(CatalogValidator::isValidTitle($title));
    }

    public function testIsValidTitleReturnsFalseForTooLong501Chars() {
        $title = '';
        for ($i = 1; $i <= 501; $i++) {
            $title .= 'A';
        }
        $this->assertFalse(CatalogValidator::isValidTitle($title));
    }

    public function testIsValidTitleReturnsFalseForNLChar() {
        $this->assertFalse(CatalogValidator::isValidTitle("costam\n"));
    }

    public function testIsValidTitleReturnsFalseForEmptyTitle() {
        $this->assertFalse(CatalogValidator::isValidTitle(''));
    }

    public function testIsValidISBNReturnsTrueForValid10DigitsWithX() {
        $this->assertTrue(CatalogValidator::isValidISBN('0-312-93033-X'));
    }

    public function testIsValidISBNReturnsTrueForValid10DigitsWith0() {
        $this->assertTrue(CatalogValidator::isValidISBN('9971-5-0210-0'));
    }

    public function testIsValidISBNReturnsFalseForInvalid10Checksum() {
        $this->assertFalse(CatalogValidator::isValidISBN('0-312-93033-1'));
    }

    public function testIsValidISBNReturnsTrueForValid13Checksum() {
        $this->assertTrue(CatalogValidator::isValidISBN('978-83-7181-510-2'));
    }

    public function testIsValidISBNReturnsFalseForInvalid13Checksum() {
        $this->assertFalse(CatalogValidator::isValidISBN('978-83-7181-510-3'));
    }

    public function testIsValidSeriesReturnTrueForAllMatchingChars() {
        $this->assertTrue(CatalogValidator::isValidSeries("A bÓóś1'\".&,-"));
    }

    public function testIsValidSeriesReturnFalseForNameWithNewLine() {
        $this->assertFalse(CatalogValidator::isValidSeries("A bÓóś1'\".&,-\n"));
    }
   
    public function testIsValidSeriesReturnFalseForEmptySeries() {
        $this->assertFalse(CatalogValidator::isValidSeries(''));
    }

    public function testIsValidSeriesReturnFalseForTooLongSeries() {
        $name = '';
        for ($i = 1; $i <= 101; $i++) {
            $name .= 'A';
        }
        $this->assertFalse(CatalogValidator::isValidSeries($name));
    }

    public function testIsValidVolumeReturnsTrueForMin1() {
        $this->assertTrue(CatalogValidator::isValidVolume(1));
    }

    public function testIsValidVolumeReturnsTrueForMax65535() {
        $this->assertTrue(CatalogValidator::isValidVolume(65535));
    }

    public function testIsValidVolumeReturnsFalseFor0() {
        $this->assertFalse(CatalogValidator::isValidVolume(0));
    }

    public function testIsValidVolumeReturnsFalseForNegative() {
        $this->assertFalse(CatalogValidator::isValidVolume(-1));
    }

    public function testIsValidVolumeThrowsExceptionForString() {
        $this->expectException('\TypeError');
        CatalogValidator::isValidVolume('string value');
    }

    public function testIsValidPagesReturnsTrueForMin1() {
        $this->assertTrue(CatalogValidator::isValidPages(1));
    }

    public function testIsValidPagesReturnsTrueForMax65535() {
        $this->assertTrue(CatalogValidator::isValidPages(65535));
    }

    public function testIsValidPagesReturnsFalseFor0() {
        $this->assertFalse(CatalogValidator::isValidPages(0));
    }

    public function testIsValidPagesReturnsFalseForNegative() {
        $this->assertFalse(CatalogValidator::isValidPages(-1));
    }

    public function testIsValidPagesThrowsExceptionForString() {
        $this->expectException('\TypeError');
        CatalogValidator::isValidPages('string value');
    }

    public function testIsValidDescriptionReturnsTrueForAllChars() {
        $partNormalChars = 'ABCDEFGabcedg';
        $partWhiteSpace = ' ';
        $partSpecialChar = 'ążźćĄŚ構成員の固有';
        $partTags = '<html>';
        $partParenthesis = '\'\"';
        $partBackslashNRT = "\n\r\t";
        $value = $partNormalChars.$partWhiteSpace.$partSpecialChar.$partTags.$partParenthesis.$partBackslashNRT;
        $this->assertTrue(CatalogValidator::isValidDescription($value));
    }

    public function testIsValidDescriptionReturnsFalseForEmpty() {
        $this->assertFalse(CatalogValidator::isValidDescription(''));
    }

    public function testIsValidDescriptionReturnsTrueForMin1Char() {
        $this->assertTrue(CatalogValidator::isValidDescription('a'));
    }

    public function testIsValidDescriptionReturnsTrueForMax10000Cars() {
        $value = '';
        for ($i = 1; $i <= 10000; $i++) {
            $value .= 'a';
        }
        $this->assertTrue(CatalogValidator::isValidDescription($value));
    }

    public function testIsValidDescriptionReturnsFalseForMorThan10kChars() {
        $value = '';
        for ($i = 1; $i <= 10001; $i++) {
            $value .= 'a';
        }
        $this->assertFalse(CatalogValidator::isValidDescription($value));
    }

    public function testIsValidCommentReturnsTrueForAllChars() {
        $partNormalChars = 'ABCDEFGabcedg';
        $partWhiteSpace = ' ';
        $partSpecialChar = 'ążźćĄŚ構成員の固有';
        $partTags = '<html>';
        $partParenthesis = '\'\"';
        $partBackslashNRT = "\n\r\t";
        $value = $partNormalChars.$partWhiteSpace.$partSpecialChar.$partTags.$partParenthesis.$partBackslashNRT;
        $this->assertTrue(CatalogValidator::isValidComment($value));
    }

    public function testIsValidCommentReturnsFalseForEmpty() {
        $this->assertFalse(CatalogValidator::isValidComment(''));
    }

    public function testIsValidCommentReturnsTrueForMin1Char() {
        $this->assertTrue(CatalogValidator::isValidComment('a'));
    }

    public function testIsValidCommentReturnsTrueForMax10000Cars() {
        $value = '';
        for ($i = 1; $i <= 10000; $i++) {
            $value .= 'a';
        }
        $this->assertTrue(CatalogValidator::isValidComment($value));
    }

    public function testIsValidCommentReturnsFalseForMorThan10kChars() {
        $value = '';
        for ($i = 1; $i <= 10001; $i++) {
            $value .= 'a';
        }
        $this->assertFalse(CatalogValidator::isValidComment($value));
    }






}