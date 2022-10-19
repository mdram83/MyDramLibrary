<?php

namespace MyDramLibrary\Controller;

use MyDramLibrary\Catalog\AuthorFactory;
use MyDramLibrary\Catalog\CategoryFactory;
use MyDramLibrary\Catalog\PublisherFactory;
use MyDramLibrary\View\MVCView;

class AjaxCatalogList extends MVCAjaxControllerImplementation
{
    protected function runNoAction(): void
    {
        $this->return404();
    }

    protected function getCategoryList(): void
    {
        $categories = [];
        foreach (CategoryFactory::instance()->getAllCategories() as $id => $category) {
            $categories[] = ['id' => $id, 'category' => $category->getName()];
        }
        $this->loadView(new MVCView('AjaxGenericJSONArray', $categories));
    }

    protected function getPublisherList(): void
    {
        $publishers = [];
        foreach (PublisherFactory::instance()->getAllPublishers() as $id => $publisher) {
            $publishers[] = ['id' => $id, 'publisher' => $publisher->getName()];
        }
        $this->loadView(new MVCView('AjaxGenericJSONArray', $publishers));
    }

    protected function getAuthorNames(): void
    {
        $data = [];
        foreach (AuthorFactory::instance()->getAllAuthors() as $authorId => $author) {
            $firstname = $author->getFirstname();
            $lastname = $author->getLastname();

            $data['author'][$authorId]['firstname'] = $firstname;
            $data['author'][$authorId]['lastname'] = $lastname;
            $data['author'][$authorId]['authorName'] = $author->getAuthorName();

            $data['firstname'][$firstname] = $firstname;
            $data['lastname'][$lastname] = $lastname;
        }
        $this->loadView(new MVCView('AjaxGenericJSONArray', $data));
    }
}
