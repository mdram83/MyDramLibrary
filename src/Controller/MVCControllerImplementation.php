<?php

namespace MyDramLibrary\Controller;

use Exception;
use MyDramLibrary\Utilities\Http\Request;
use MyDramLibrary\View\MVCView;

abstract class MVCControllerImplementation implements MVCController
{
    protected MVCView $view;
    protected Request $request;
    protected ?string $action = null;

    final public function __construct()
    {
        $this->request = Request::instance();
        $this->action = $this->request->getGetParam('action') ?? 'runNoAction';
        $this->extendConstructor();
    }

    final public function run(): void
    {
        if (!method_exists($this, $this->action) || $this->action == 'run') {
            throw new Exception('Incorrect action'); // trzeba gdzies to obsluzyc
        }
        $this->{$this->action}();
    }

    final protected function loadView(MVCView $view): void
    {
        $this->view = $view;
        $this->view->loadPageContent();
        $this->view->showPageContent();
    }

    abstract protected function extendConstructor(): void;
    abstract protected function runNoAction(): void;
}
