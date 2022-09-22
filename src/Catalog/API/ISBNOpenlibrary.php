<?php

namespace MyDramLibrary\Catalog\API;

use MyDramLibrary\CustomException\ValidatorException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use MyDramLibrary\Utilities\API\RestAPIHandlerBase;
use MyDramLibrary\Utilities\Validator\CatalogValidator;

class ISBNOpenlibrary extends RestAPIHandlerBase
{
    private Client $client;
    private bool $sent = false;

    public function __construct(?Client $client, string $isbn)
    {
        if (!CatalogValidator::isValidISBN($isbn)) {
            throw new ValidatorException('API issue: invalid ISBN parameter');
        }
        $this->isbn = $isbn;
        $this->setURI("https://openlibrary.org/api/books?bibkeys=ISBN:$isbn&jscmd=details&format=json");
        $this->setMethod('GET');

        if (isset($client)) {
            $this->client = $client;
        } else {
            $this->client = new Client();
        }
    }

    public function setURI(string $address): void
    {
        if (isset($this->uri)) {
            throw new Exception('API issue: URI already set');
        }
        parent::setURI($address);
    }

    public function setMethod(string $method): void
    {
        if (isset($this->method)) {
            throw new Exception('API issue: Method already set');
        }
        parent::setMethod($method);
    }

    protected function setResponseCode(int $responseCode): void
    {
        $this->responseCode = $responseCode;
    }

    public function getResponseCode(): int
    {
        if (!$this->sent) {
            $this->send();
        }
        return parent::getResponseCode();
    }

    protected function setResponseContent(mixed $responseContent): void
    {
        $this->responseContent = $responseContent;
    }

    public function getResponseContent(): mixed
    {
        if (!$this->sent) {
            $this->send();
        }
        return parent::getResponseContent();
    }

    public function send(): bool
    {
        if ($this->sent) {
            throw new Exception('API issue: Request already sent');
        }
        try {
            $this->sent = true;
            $response = $this->client->request($this->getMethod(), $this->uri);
            if ($response->getBody() == '{}') {
                $this->consider404();
                return false;
            }
            $this->setResponseCode($response->getStatusCode());
            $this->setResponseContent($response->getBody());
            return true;
        } catch (ClientException $e) {
            $this->consider404();
            return false;
        }
    }

    private function consider404(): void
    {
        $this->setResponseCode(404);
        $this->setResponseContent(null);
    }
}
