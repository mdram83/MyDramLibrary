<?php

namespace MyDramLibrary\Controller;

use MyDramLibrary\Catalog\API\ISBNOpenlibrary;
use MyDramLibrary\CustomException\ValidatorException;
use Exception;
use MyDramLibrary\User\UserSession;
use MyDramLibrary\Utilities\API\RestAPIHandler;
use MyDramLibrary\View\MVCView;

class AjaxExternalAPIRouter extends MVCAjaxControllerImplementation
{
    protected UserSession $userSession;
    protected RestAPIHandler $apiHandler;

    protected function extendConstructor(): void
    {
        $this->userSession = UserSession::instance();
        $this->verifyLoggedUser();
        parent::extendConstructor();
    }

    protected function verifyLoggedUser(): void
    {
        if (!$this->userSession->isLoggedIn()) {
            $this->return404();
        }
    }

    protected function runNoAction(): void
    {
        $this->return404();
    }

    protected function getDetailsWithISBNOpenlibrary(): void
    {
        $isbn = str_replace('-', '', $this->request->getPostParamTrim('isbn'));
        try {
            $this->apiHandler = new ISBNOpenlibrary(null, $isbn);
            $responseCode = $this->apiHandler->getResponseCode();
            $responseContent = $this->apiHandler->getResponseContent();
            if ($responseCode == 200) {
                $details = $this->parseISBNOpenlibraryDetails($isbn, $responseContent);
                $this->loadView(new MVCView('AjaxGenericJSONArray', $details));
            } else {
                $this->return404();
            }
        } catch (ValidatorException|Exception) {
            $this->return404();
        }
    }

    // TODO: not te right class for this method...
    private function parseISBNOpenlibraryDetails(string $isbn, string $responseContent): array
    {
        $responseArray = json_decode($responseContent, true);
        $responseArray = $responseArray["ISBN:$isbn"]['details'];
        $details = [
            'title' => $responseArray['title'] ?? null,
            'authorFirstname' => null,
            'authorLastname' => null,
            'isbn' => $isbn,
            'publisher' => $responseArray['publishers'][0] ?? null,
            'series' => null,
            'volume' => null,
            'pages' => $responseArray['number_of_pages'] ?? null,
            'category' => $responseArray['subjects'] ?? null,
        ];

        $author = $responseArray['authors'][0]['name'] ?? null;
        if ($author) {
            $splitPosition = strrpos($author, ' ');
            $details['authorFirstname'] = trim(substr($author, 0, $splitPosition));
            $details['authorLastname'] = trim(substr($author, $splitPosition));
        }

        $series = $responseArray['series'][0] ?? null;
        if ($series) {
            $splitPosition = strrpos($series, ', #');
            $details['series'] = ($splitPosition !== false) ? trim(substr($series, 0, $splitPosition)) : trim($series);
            $details['volume'] = ($splitPosition !== false) ? trim(substr($series, $splitPosition + 3)) : null;
        }

        return $details;
    }
}
