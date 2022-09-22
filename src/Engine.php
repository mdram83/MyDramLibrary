<?php


namespace MyDramLibrary;

use Exception;
use MyDramLibrary\User\UserSession;
use MyDramLibrary\Utilities\Database\Database;
use MyDramLibrary\Utilities\Http\Request;

class Engine
{
    private static ?Engine $instance = null;
    private Request $request;
    private UserSession $userSession;

    private function __construct()
    {
        $this->loadConfiguration();
        $this->initiateDatabase();
        $this->initiateSession();
        $this->initiateController();
    }

    public static function instance(): Engine
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfiguration(): void
    {
        // constants, e.g. default Controller
        // db credentials
    }

    private function initiateDatabase(): void
    {
        // and error handling (exception handling)
        Database::instance();
    }

    private function initiateSession(): void
    {
        $this->request = Request::instance();
        $this->userSession = UserSession::instance();
    }

    private function initiateController(): void
    {
        $defaultModuleName = ($this->userSession->isLoggedIn()) ? 'Catalog' : 'Home';
        // remember to change home to default controller when defined not to hardcode here;
        // Think of setting up different default controller for logged and not logged user and handle that
        // And don't use string path below but from configuration instead

        $moduleName = ucfirst(strtolower($this->request->getGetParam('module') ?? $defaultModuleName));
        try {
            $controllerClassName = "MyDramLibrary\\Controller\\$moduleName";
            $controller = new $controllerClassName();
            // above doesn't work as this throws fatal error and not exception.
            // need to rebuild to check if there is a class and maybe inside controller (run) check if there is public method?
        } catch (Exception) {
            $controllerClassName = "MyDramLibrary\\Controller\\$defaultModuleName";
            $controller = new $controllerClassName();
        }
        $controller->run();
    }
}
