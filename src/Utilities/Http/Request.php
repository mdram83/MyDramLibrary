<?php

namespace MyDramLibrary\Utilities\Http;

class Request
{
    protected static ?Request $instance = null;
    private $getParams = array();
    private $postParams = array();
    private $files = array();
    private ?string $referer;
    private string $requestedUrl;
    private bool $isAjax = false;

    private function __construct()
    {
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->files = $_FILES;
        $this->referer = $_SERVER['HTTP_REFERER'] ?? null;
        $this->host = $_SERVER['HTTP_HOST'];
        $this->requestedUrl = $this->host . $_SERVER['REQUEST_URI'];
        $this->isAjax = (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
            ) ?? false;
    }

    public static function instance(): Request
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getReferer(): mixed
    {
        return $this->referer;
    }

    public function __toString(): string
    {
        return $this->getRequestedUrl();
    }

    public function getRequestedUrl(): string
    {
        return $this->requestedUrl;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getGetParam(string $name): mixed
    {
        return $this->getParams[$name] ?? null;
    }

    public function getPostParam(string $name): mixed
    {
        return $this->postParams[$name] ?? null;
    }

    public function getPostParams(): array|null
    {
        return $this->postParams ?? null;
    }

    public function getPostParamTrim(string $name): ?string
    {
        return trim($this->getPostParam($name)) ?? null;
    }

    public function getFileParam(string $name): mixed
    {
        return $this->files[$name] ?? null;
    }

    public function isAjaxRequest(): bool
    {
        return $this->isAjax;
    }
}
