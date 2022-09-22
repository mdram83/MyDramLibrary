<?php

namespace MyDramLibrary\Utilities\API;

use MyDramLibrary\Utilities\Validator\APIValidator;

abstract class RestAPIHandlerBase implements RestAPIHandler
{
    protected ?string $uri = null;
    protected ?string $method = null;
    protected ?int $responseCode = null;
    protected mixed $responseContent = null;

    public function setURI(string $address): void
    {
        if (APIValidator::isValidURI($address)) {
            $this->uri = $address;
        } else {
            throw new \Exception('API issue: Incorrect URI parameter');
        }
    }

    public function getURI(): ?string
    {
        return $this->uri;
    }

    public function setMethod(string $method): void
    {
        if (APIValidator::isValidMethod($method)) {
            $this->method = $method;
        } else {
            throw new \Exception('API issue: Incorrect HTTP method parameter');
        }
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    abstract protected function setResponseCode(int $responseCode): void;
    abstract protected function setResponseContent(mixed $responseContent): void;

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function getResponseContent(): mixed
    {
        return $this->responseContent;
    }
}
