<?php

namespace MyDramLibrary\Controller;

use Exception;
use PDOException;
use MyDramLibrary\User\User;
use MyDramLibrary\Utilities\Communication\EmailCommunication;
use MyDramLibrary\View\MVCFullView;
use MyDramLibrary\View\MVCView;

class Registration extends MVCControllerImplementation
{
    private User $user;

    protected function extendConstructor(): void
    {
    }

    protected function runNoAction(): void
    {
        $this->loadView(new MVCFullView('RegistrationForm', array()));
    }

    protected function register(): void
    {
        $data = array();
        try {
            $this->user = new User();
            $this->user->setUsername($this->request->getPostParamTrim('username'));
            $this->user->setEmail($this->request->getPostParamTrim('email'));
            $this->user->setPassword($this->request->getPostParamTrim('password'));

            $emailData['hash'] = $this->user->registerUser();
            $emailData['id'] = $this->user->getUserId();

            $communication = new EmailCommunication();
            $communication->addRecipient($this->user->getEmail());
            $communication->setSender('registration@test.com'); // z konfigu
            $communication->setSubject('Please verify your registration');

            // dorób ładniejsze templatey (do EMAIL oraz poniżej do Verify)
            $emailTemplate = new MVCView('EMAIL_Registration', $emailData);
            $emailTemplate->loadPageContent();
            $communication->setContent($emailTemplate->getPageContent());
            $communication->send();

            $data['failed'] = false;
        } catch (PDOException $e) {
            $data['message'] = 'Failed to register user account.';
            $data['failed'] = true;
        } catch (Exception $e) {
            $data['message'] = $e->getMessage();
            $data['failed'] = true;
        }

        $this->loadView(new MVCFullView('RegistrationComplete', $data));
    }

    protected function verify(): void
    {
        $data = array();
        try {
            if (!$this->request->getGetParam('id') || !$this->request->getGetParam('hash')) {
                throw new Exception('Incorrect link');
            }
            $this->user = new User($this->request->getGetParam('id'));
            if ($this->user->verify($this->request->getGetParam('hash'))) {
                $data['failed'] = false;
                $data['message'] = null;
            } else {
                $data['failed'] = true;
                $data['message'] = 'Incorrect link';
            }
        } catch (Exception $e) {
            $data['failed'] = true;
            $data['message'] = "Verification failed: {$e->getMessage()}";
        }

        $this->loadView(new MVCFullView('VerificationComplete', $data));
    }
}
