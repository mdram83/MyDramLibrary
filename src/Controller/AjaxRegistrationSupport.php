<?php

namespace MyDramLibrary\Controller;

use MyDramLibrary\User\UserLookup;
use MyDramLibrary\View\MVCView;

class AjaxRegistrationSupport extends MVCAjaxControllerImplementation
{
    private ?string $lookupParam;
    private UserLookup $userLookup;

    protected function extendConstructor(): void
    {
        parent::extendConstructor();
        $this->lookupParam = $this->request->getPostParamTrim('user') ?? null;
        $this->userLookup = new UserLookup();
    }

    protected function runNoAction(): void
    {
        $this->return404();
    }

    protected function searchUserId(): void
    {
        $this->setUserLookupValue();
        $this->loadView(new MVCView('AjaxSearchUserId', ['userId' => $this->userLookup->getUserId()]));
    }

    protected function userExists(): void
    {
        $this->setUserLookupValue();
        $this->loadView(new MVCView('AjaxUserExists', ['userExists' => ($this->userLookup->getUserId() > 0) ? 1 : 0]));
    }

    private function setUserLookupValue(): void
    {
        if (!isset($this->lookupParam)) {
            $this->return404();
            exit();
        }
        $this->userLookup->setLookupValue($this->lookupParam);
    }
}
