<?php

namespace MyDramLibrary\Catalog;

use InvalidArgumentException;
use MyDramLibrary\Utilities\Collection\Collection;

class CategoryCollection extends Collection
{
    public function addItem($categoryObject, $key = null): void
    {
        if (get_class($categoryObject) != 'MyDramLibrary\Catalog\Category') {
            throw new InvalidArgumentException('Category object expected');
        }
        parent::addItem($categoryObject, $key);
    }
}
