<?php

namespace MyDramLibrary\Catalog;

use InvalidArgumentException;
use MyDramLibrary\Utilities\Collection\Collection;

class PublisherCollection extends Collection
{
    public function addItem($publisherObject, $key = null): void
    {
        if (get_class($publisherObject) != 'MyDramLibrary\Catalog\Publisher') {
            throw new InvalidArgumentException('Publisher object expected');
        }
        parent::addItem($publisherObject, $key);
    }
}
