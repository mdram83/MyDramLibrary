<?php

namespace MyDramLibrary\View;

class CatalogView extends MVCView
{
    protected function enrichContent(): void
    {
        $this->addPageContent('HTML_CatalogPageTopBar', post: false);
        $this->addPageContent('HTML_BODYOpen', post: false);
        $this->addPageContent('HTML_HEADClose', post: false);
        $this->addPageContent('HTML_HEADScript', ['script' => 'catalog.js'], post: false);
        $this->addPageContent('HTML_HEADStylesheet', ['stylesheet' => 'catalog.css'], post: false);
        $this->addPageContent('HTML_HEADCommonContent', post: false);
        $this->addPageContent('HTML_HEADTitle', $this->templateData['htmlHead'], post: false);
        $this->addPageContent('HTML_HEADOpen', post: false);
        $this->addPageContent('HTML_HTMLOpen', post: false);
        $this->addPageContent('HTML_BODYClose');
        $this->addPageContent('HTML_HTMLClose');
    }

    protected function processContent(): void
    {
        $template = new HTMLTemplate($this->pageContent, $this->templateData);
        $this->pageContent = $template->process();
    }
}
