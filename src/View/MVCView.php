<?php

namespace MyDramLibrary\View;

use MyDramLibrary\Configuration\DirectoryConfiguration;

class MVCView
{
    protected string $templateFilePath;
    protected array $templateData;
    protected ?string $pageContent = null;

    public function __construct(string $filename, array $data = array())
    {
        $filePath = DirectoryConfiguration::templatesPath() . $filename . '.php';
        if (file_exists($filePath)) {
            $this->templateFilePath = $filePath;
        } else {
            throw new \Exception('Template file missing: ' . $filePath);
            // Może zastosować jakąś bardziej specyfuczną klasę dziedziczącą po Exception?
        }
        $this->templateData = $data;
    }

    public function loadPageContent(): void
    {
        $data = $this->templateData;
        ob_start();
        require $this->templateFilePath;
        $this->pageContent = ob_get_clean();
        $this->enrichContent();
        $this->processContent();
    }

    public function showPageContent(): void
    {
        echo $this->getPageContent();
    }

    public function getPageContent(): string
    {
        return $this->pageContent;
        // wywalaj coś jeżeli nie ustawiony (nie loaded); lub zmienić load na private i wymuszac przy get ale tylko raz?
    }

    protected function addPageContent(string $filename, array $data = array(), bool $post = true): void
    {
        $MVCView = new MVCView($filename, $data);
        $MVCView->loadPageContent();
        $this->pageContent =
            $post
            ? $this->getPageContent() . $MVCView->getPageContent()
            : $MVCView->getPageContent() . $this->getPageContent();
    }

    protected function enrichContent(): void
    {
    }

    protected function processContent(): void
    {
    }
}
