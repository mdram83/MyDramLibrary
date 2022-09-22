<?php

namespace MyDramLibrary\Controller;

use MyDramLibrary\CustomException\LoginInactiveUserException;
use MyDramLibrary\CustomException\LoginUnverifiedUserException;
use Exception;
use MyDramLibrary\Registration\UnsignedUserDashboard;
use MyDramLibrary\User\UserLogin;
use MyDramLibrary\View\MVCFullView;
use MyDramLibrary\View\MVCView;

class Home extends MVCControllerImplementation
{
    protected MVCView $view;
    private UnsignedUserDashboard $dashbaord;
    private array $data = array();

    protected function extendConstructor(): void
    {
    }

    protected function runNoAction(): void
    {
        $this->dashbaord = new UnsignedUserDashboard();
        $this->data['titleCount'] = $this->dashbaord->getTitleCountRounded();
        $this->data['userCount'] = $this->dashbaord->getUserCountRounded();
        $this->data['cityCount'] = $this->dashbaord->getCityCountRounded();
        $this->loadView(new MVCFullView('UnsignedUserHomePage', $this->data));
    }

    protected function login(): void
    {
        $email = $this->request->getPostParamTrim('email');
        $password = $this->request->getPostParam('password');

        if (!$email || !$password) {
            header('Location: /');
            exit();
        }

        try {
            $userLogin = new UserLogin();
            if ($userLogin->logInWithEmail($email, $password)) {
                header('Location: /'); // lokalizacja dla zalogowanego usera (ustawienie tutaj lub w engine)
                exit();
            } else {
                $this->data['failureMessage'] = 'Incorrect email or/and password';
            }
        } catch (LoginUnverifiedUserException|LoginInactiveUserException $e) {
            $this->data['failureMessage'] = $e->getMessage();
        } catch (Exception $e) {
            $this->data['failureMessage'] = 'Something went wrong, please try again later';
        }
        $this->data['failedLogin'] = true;
        $this->data['email'] = $email;
        $this->runNoAction();
    }
}
