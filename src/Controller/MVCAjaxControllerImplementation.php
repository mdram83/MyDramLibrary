<?php

namespace MyDramLibrary\Controller;

abstract class MVCAjaxControllerImplementation extends MVCControllerImplementation
{
    protected function extendConstructor(): void
    {
        $this->abortRequestIfNotAjax();
    }

    final protected function abortRequestIfNotAjax(): void
    {
        if (!$this->request->isAjaxRequest()) {
            header('Location: /');
            exit();
        }
    }

    final protected function return403(): void
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
        exit();
    }

    final protected function return404(): void
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
        exit();
    }
}
