<?php

namespace MyDramLibrary\Controller;

use MyDramLibrary\User\UserSession;

abstract class MVCControllerImplementationLoggedUser extends MVCControllerImplementation
{
    protected UserSession $userSession;

    protected function extendConstructor(): void
    {
        $this->userSession = UserSession::instance();
        $this->verifyLoggedUser();
    }

    protected function verifyLoggedUser(): void
    {
        if (!$this->userSession->isLoggedIn()) {
            header('Location: /');
            exit();
        }
    }
}
