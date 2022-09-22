<?php

namespace MyDramLibrary\View;

abstract class HTMLTemplateConfig
{
    public const TEMPLATE_TAG_OPEN_BRACKET = '{';
    public const TEMPLATE_TAG_CLOSE_BRACKET = '}';

    public const TEMPLATE_SECTION_LOOP_TAG = 'loop';
    public const TEMPLATE_SECTION_IF_TAG = 'if';
    public const TEMPLATE_SECTION_IFNOT_TAG = 'ifnot';
    public const TEMPLATE_SECTION_ROLL_TAG = 'roll';
    public const TEMPLATE_SECTION_ROLL_SEPARATOR = 'to';
    public const TEMPLATE_FILE_TAG = 'file';
    public const TEMPLATE_SEPARATOR = ':';
    public const TEMPLATE_SECTION_END_CHAR = '/';

    public const TEMPLATE_FUNCTION_SEPARATOR = '|';
    public const TEMPLATE_FUNCTION_INDEX_TAG = 'index';
    public const TEMPLATE_FUNCTION_ENCODE_TAG = 'encode';

    public const TEMPLATE_NESTED_VAR_SEPARATOR = '.';

    public const TEMPLATE_EREG_PATTERN_FUNCTION_INDEX =
        '(?:'
        . '\\'
        . self::TEMPLATE_FUNCTION_SEPARATOR
        . self::TEMPLATE_FUNCTION_INDEX_TAG
        . ')?';
    public const TEMPLATE_EREG_PATTERN_FUNCTION_ENCODE =
        '(?:'
        . '\\'
        . self::TEMPLATE_FUNCTION_SEPARATOR
        . self::TEMPLATE_FUNCTION_ENCODE_TAG
        . ')?';
    public const TEMPLATE_EREG_PATTERN_FUNCTION =
        self::TEMPLATE_EREG_PATTERN_FUNCTION_INDEX
        . self::TEMPLATE_EREG_PATTERN_FUNCTION_ENCODE;

    public const TEMPLATE_EREG_PATTERN_VARIABLE_NAME = '[a-zA-Z0-9_.]+';
    public const TEMPLATE_EREG_PATTERN_FILE_NAME = '[a-zA-Z0-9_.\/]+';
    public const TEMPLATE_EREG_PATTERN_NUMBER = '[0-9]+';
    public const TEMPLATE_EREG_PATTERN_ROLL_NUMBERS =
        self::TEMPLATE_EREG_PATTERN_NUMBER
        . '(?:'
        . self::TEMPLATE_SECTION_ROLL_SEPARATOR
        . self::TEMPLATE_EREG_PATTERN_NUMBER
        . ')?';

    public const TEMPLATE_EREG_PATTERN_LOOP_START =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . self::TEMPLATE_SECTION_LOOP_TAG
        . self::TEMPLATE_SEPARATOR
        . self::TEMPLATE_EREG_PATTERN_VARIABLE_NAME
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_LOOP_END =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . '\\' . self::TEMPLATE_SECTION_END_CHAR
        . self::TEMPLATE_SECTION_LOOP_TAG
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_IF_START =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . self::TEMPLATE_SECTION_IF_TAG
        . self::TEMPLATE_SEPARATOR
        . self::TEMPLATE_EREG_PATTERN_VARIABLE_NAME
        . self::TEMPLATE_EREG_PATTERN_FUNCTION
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_IF_END =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . '\\' . self::TEMPLATE_SECTION_END_CHAR
        . self::TEMPLATE_SECTION_IF_TAG
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_IFNOT_START =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . self::TEMPLATE_SECTION_IFNOT_TAG
        . self::TEMPLATE_SEPARATOR
        . self::TEMPLATE_EREG_PATTERN_VARIABLE_NAME
        . self::TEMPLATE_EREG_PATTERN_FUNCTION
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_IFNOT_END =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . '\\' . self::TEMPLATE_SECTION_END_CHAR
        . self::TEMPLATE_SECTION_IFNOT_TAG
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_ROLL_START =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . self::TEMPLATE_SECTION_ROLL_TAG
        . self::TEMPLATE_SEPARATOR
        . self::TEMPLATE_EREG_PATTERN_ROLL_NUMBERS
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_ROLL_END =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . '\\' . self::TEMPLATE_SECTION_END_CHAR
        . self::TEMPLATE_SECTION_ROLL_TAG
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_FILE_PATH =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . self::TEMPLATE_FILE_TAG
        . self::TEMPLATE_SEPARATOR
        . self::TEMPLATE_EREG_PATTERN_FILE_NAME
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_INDEX =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . '\\' . self::TEMPLATE_FUNCTION_SEPARATOR
        . self::TEMPLATE_FUNCTION_INDEX_TAG
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_EREG_PATTERN_VARIABLE =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . self::TEMPLATE_SEPARATOR
        . self::TEMPLATE_EREG_PATTERN_VARIABLE_NAME
        . self::TEMPLATE_EREG_PATTERN_FUNCTION
        . self::TEMPLATE_TAG_CLOSE_BRACKET;

    public const TEMPLATE_SECTION_END_OPENING =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . self::TEMPLATE_SECTION_END_CHAR;

    public const TEMPLATE_VARIABLE_OPENING =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . self::TEMPLATE_SEPARATOR;

    public const TEMPLATE_FILE_OPENING =
        self::TEMPLATE_TAG_OPEN_BRACKET
        . self::TEMPLATE_SEPARATOR
        . self::TEMPLATE_FILE_TAG;
}
