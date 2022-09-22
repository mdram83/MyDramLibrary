<?php

namespace MyDramLibrary\Utilities\Validator;

class APIValidator extends Validator
{
    public static function isValidURI(string $address): bool
    {
        return (filter_var($address, FILTER_VALIDATE_URL)) ? true : false;
    }

    public static function isValidMethod(string $method): bool
    {
        $HttpMethods = array('GET', 'POST', 'PUT', 'DELETE', 'PATCH');
        return in_array($method, $HttpMethods, true);
    }
}
