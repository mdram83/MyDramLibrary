<?php

namespace MyDramLibrary\Controller;

use MyDramLibrary\Catalog\Author;
use MyDramLibrary\Catalog\CategoryFactory;
use MyDramLibrary\Catalog\Publisher;
use MyDramLibrary\Catalog\Title;
use MyDramLibrary\Catalog\TitleFactory;
use MyDramLibrary\CustomException\ValidatorException;
use Exception;
use MyDramLibrary\User\User;
use MyDramLibrary\Utilities\Http\Request;
use MyDramLibrary\View\CatalogView;
use MyDramLibrary\View\MVCView;

class Catalog extends MVCControllerImplementationLoggedUser
{
    protected MVCView $view;
    private array $data = array();

    protected function extendConstructor(): void
    {
        parent::extendConstructor();
        $this->data['htmlHead']['title'] = 'My Library - Catalog';
    }

    protected function runNoAction(): void
    {
        $this->list();
    }

    private function errorPage(?string $message): void
    {
        $this->data['status'] = 0;
        $this->data['message'] = $message;
        $this->loadView(new CatalogView('CatalogErrorMessage', $this->data));
        exit();
    }

    protected function list(): void
    {

        $titleCollection = TitleFactory::instance()->getUserTitles(new User($this->userSession->getUserId()));
        foreach ($titleCollection as $key => $title) {
            $this->data['titles'][$key] = $title->getDataArray();
        }

        if ($this->request->getGetParam('deleted') == 'confirmed') {
            $this->data['status'] = 1;
            $this->data['message'] = "Title deleted from your library.";
        }

        $this->data['htmlHead']['title'] = 'My Library - My Titles';
        $this->loadView(new CatalogView('CatalogUserTitleList', $this->data));
    }

    protected function add(): void
    {
        if ($this->request->getPostParam('submit') == null) {
            $this->data['htmlHead']['title'] = 'My Library - Add Title';
            $this->loadView(new CatalogView('CatalogAddTitleForm', $this->data));
            exit();
        }

        try {
            $title = TitleFactory::instance()->createUserTitleFromRequet(
                new User($this->userSession->getUserId()),
                $this->request
            );
            header('Location: /?module=Catalog&action=edit&title=' . $title->getId() . '&added=confirmed');
            exit();
        } catch (ValidatorException $e) {
            $this->data['status'] = 0;
            $this->data['message'] = $e->getMessage();
        } catch (Exception) {
            $this->data['status'] = 0;
            $this->data['message'] = "Ups... We encountered unexpected error when saving your Title.";
        }

        if (isset($this->data['status']) && $this->data['status'] === 0) {
            $this->data['formValues'] = $this->request->getPostParams();
            $this->data['htmlHead']['title'] = 'My Library - Add Title';
            $this->loadView(new CatalogView('CatalogAddTitleForm', $this->data));
        }
    }

    protected function edit()
    {
        if ($this->request->getPostParam('submit') == null) {
            if ($this->request->getGetParam('title') == '') {
                $this->errorPage("Ups... We encountered unexpected error when loading your Title.");
            }

            try {
                $title = new Title($this->request->getGetParam('title'), new User($this->userSession->getUserId()));
                $this->data['title'] = $title->getDataArray();

                if ($this->request->getGetParam('added') == 'confirmed') {
                    $this->data['status'] = 1;
                    $this->data['message'] = "Title added to your library.";
                }
            } catch (Exception) {
                $this->data['status'] = 0;
                $this->data['message'] = "Ups... We encountered unexpected error when loading your Title.";
                // error page?
            }

            $this->data['title'] = $title->getDataArray();
            $this->data['htmlHead']['title'] = "My Library - My Titles - {$title->getTitle()}";
            $this->loadView(new CatalogView('CatalogEditTitleForm', $this->data));
        } else {
            if ($this->request->getPostParam('id') == '') {
                $this->errorPage("Ups... We encountered unexpected error when updating your Title.");
            }

            try {
                $title = new Title($this->request->getPostParam('id'), new User($this->userSession->getUserId()));

                try {
                    $title->setTitle($this->request->getPostParamTrim('title'));
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                try {
                    $title->setAuthor(
                        (
                            $this->request->getPostParamTrim('authorFirstname') != ''
                            && $this->request->getPostParamTrim('authorLastname') != ''
                        )
                        ? new Author(
                            null,
                            $this->request->getPostParamTrim('authorFirstname'),
                            $this->request->getPostParamTrim('authorLastname')
                        )
                        : null
                    );
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                try {
                    $title->setPublisher(
                        ($this->request->getPostParamTrim('publisher') != '')
                        ? new Publisher(null, $this->request->getPostParamTrim('publisher'))
                        : null
                    );
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                try {
                    $title->setISBN(
                        ($this->request->getPostParamTrim('isbn') != '')
                        ? $this->request->getPostParamTrim('isbn')
                        : null
                    );
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                try {
                    $title->setSeries(
                        ($this->request->getPostParamTrim('series') != '')
                        ? $this->request->getPostParamTrim('series')
                        : null
                    );
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                try {
                    $title->setVolume(
                        ($this->request->getPostParamTrim('volume') != '')
                        ? $this->request->getPostParamTrim('volume')
                        : null
                    );
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                try {
                    $title->setPages(
                        ($this->request->getPostParamTrim('pages') != '')
                        ? $this->request->getPostParamTrim('pages')
                        : null
                    );
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                try {
                    $title->setDescription(
                        ($this->request->getPostParamTrim('description') != '')
                        ? $this->request->getPostParamTrim('description')
                        : null
                    );
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                try {
                    $title->setComment(
                        ($this->request->getPostParamTrim('comment') != '')
                        ? $this->request->getPostParamTrim('comment')
                        : null
                    );
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                try {
                    $title->setCategories(
                        CategoryFactory::instance()->createCategoryCollectionFromNamesArray(
                            $this->request->getPostParam('category')
                        )
                    );
                    $this->data['message'] = "Title updated<br>";
                } catch (ValidatorException $e) {
                    $this->data['validatorMessage'][] = $e->getMessage();
                }

                if (isset($this->data['validatorMessage'])) {
                    $this->data['status'] = 0;
                    foreach ($this->data['validatorMessage'] as $message) {
                        $this->data['message'] .= "$message\n";
                    }
                } else {
                    $this->data['status'] = 1;
                }
            } catch (Exception $e) {
                $this->data['status'] = 0;
                $this->data['message'] = "Ups... We encountered unexpected error when saving your Title.";
            }

            $this->data['title'] = $this->getTitleDataFromRequest($this->request);
            $this->data['htmlHead']['title'] = "My Library - My Titles - {$this->data['title']['title']}";
            $this->loadView(new CatalogView('CatalogEditTitleForm', $this->data));
        }
    }

    protected function delete(): void
    {
        if (!$titleId = $this->request->getGetParam('title')) {
            $this->errorPage("Ups... We encountered unexpected error when deleting Title.");
        }
        try {
            $title = new Title($titleId, new User($this->userSession->getUserId()));
            $title->deleteTitle();
            header('Location: /?module=Catalog&deleted=confirmed');
        } catch (Exception $e) {
            $this->errorPage("Ups... We encountered unexpected error when deleting Title.");
        }
    }

    private function getTitleDataFromRequest(Request $request): array
    {
        foreach ($request->getPostParam('category') as $category) {
            if ($category != '') {
                $categories[] = $category;
            }
        }
        return [
            'id'              => $request->getPostParam('id'),
            'title'           => $request->getPostParamTrim('title'),
            'authorFirstname' => $request->getPostParamTrim('authorFirstname'),
            'authorLastname'  => $request->getPostParamTrim('authorLastname'),
            'author'          => null,
            'publisher'       => $request->getPostParamTrim('publisher'),
            'isbn'            => $request->getPostParamTrim('isbn'),
            'series'          => $request->getPostParamTrim('series'),
            'volume'          => $request->getPostParamTrim('volume'),
            'pages'           => $request->getPostParamTrim('pages'),
            'description'     => $request->getPostParamTrim('description'),
            'comment'         => $request->getPostParamTrim('comment'),
            'category'        => $categories ?? null,
        ];
    }
}
