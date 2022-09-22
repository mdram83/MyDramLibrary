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
        $categoryCollection = CategoryFactory::instance()->getAllCategories();
        $categoryCounter = 0;
        foreach ($categoryCollection as $id => $category) {
            $categories[$categoryCounter]['id'] = $id;
            $categories[$categoryCounter]['category'] = $category->getName();
            $categoryCounter++;
        }
        $this->loadView(new MVCView('AjaxGenericJSONArray', $categories ?? array()));
    }

    protected function getPublisherList(): void
    {
        $publisherCollection = PublisherFactory::instance()->getAllPublishers();
        $publisherCounter = 0;
        foreach ($publisherCollection as $id => $publisher) {
            $publishers[$publisherCounter]['id'] = $id;
            $publishers[$publisherCounter]['publisher'] = $publisher->getName();
            $publisherCounter++;
        }
        $this->loadView(new MVCView('AjaxGenericJSONArray', $publishers ?? array()));
    }

    protected function getAuthorNames(): void
    {
        foreach (AuthorFactory::instance()->getAllAuthors() as $authorId => $author) {
            $firstname = $author->getFirstname();
            $lastname = $author->getLastname();
            $data['author'][$authorId]['firstname'] = $firstname;
            $data['author'][$authorId]['lastname'] = $lastname;
            $data['author'][$authorId]['authorName'] = $author->getAuthorName();
            $data['firstname'][$firstname] = $firstname;
            $data['lastname'][$lastname] = $lastname;
        }
        $this->loadView(new MVCView('AjaxGenericJSONArray', $data ?? array()));
    }
}
