<?php

namespace MyDramLibrary\Catalog;

use InvalidArgumentException;
use MyDramLibrary\Utilities\Collection\Collection;

class TitleCollection extends Collection
{
    public function addItem($titleObject, $key = null): void
    {
        if (get_class($titleObject) != 'MyDramLibrary\Catalog\Title') {
            throw new InvalidArgumentException('Title object expected');
        }
        parent::addItem($titleObject, $key);
    }
}
