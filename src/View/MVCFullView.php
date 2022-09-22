<?php

namespace MyDramLibrary\View;

class MVCFullView extends MVCView
{
    protected function enrichContent(): void
    {
        $this->addPageContent('HTML_BODYOpen', post: false);
        $this->addPageContent('HTML_HEAD', post: false);
        $this->addPageContent('HTML_HTMLOpen', post: false);
        $this->addPageContent('HTML_BODYClose');
        $this->addPageContent('HTML_HTMLClose');
    }
}
