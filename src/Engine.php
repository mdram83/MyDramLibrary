<?php


namespace MyDramLibrary;

use Exception;
use MyDramLibrary\Configuration\ControllerConfiguration;
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
        $defaultModuleName =
            ($this->userSession->isLoggedIn()) ?
                ControllerConfiguration::DEFAULT_LOGGED_USER :
                ControllerConfiguration::DEFAULT_NOT_LOGGED_USER;
        $moduleName = ucfirst(strtolower($this->request->getGetParam('module') ?? $defaultModuleName));
        try {
            $controllerClassName = ControllerConfiguration::CONTROLLER_NAMESPACE . $moduleName;
            $controller = new $controllerClassName();
            // above doesn't work as this throws fatal error and not exception.
            // need to rebuild to check if there is a class
            // and maybe inside controller (run) check if there is public method?
        } catch (Exception) {
            $controllerClassName = ControllerConfiguration::CONTROLLER_NAMESPACE . $defaultModuleName;
            $controller = new $controllerClassName();
        }
        $controller->run();
    }
}
