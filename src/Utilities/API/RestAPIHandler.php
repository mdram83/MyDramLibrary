<?php

namespace MyDramLibrary\Utilities\API;

interface RestAPIHandler
{
    public function send(): bool;
    public function getResponseCode(): int;
    public function getResponseContent(): mixed;
}
