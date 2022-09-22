<?php

namespace MyDramLibrary\Catalog;

use InvalidArgumentException;
use MyDramLibrary\Utilities\Collection\Collection;

class AuthorCollection extends Collection
{
    public function addItem($object, $key = null): void
    {
        if (get_class($object) != 'MyDramLibrary\Catalog\Author') {
            throw new InvalidArgumentException('Author object expected');
        }
        parent::addItem($object, $key);
    }
}
