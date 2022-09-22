<?php

namespace MyDramLibrary\View;

use InvalidArgumentException;
use MyDramLibrary\Utilities\Collection\Collection;

class HTMLTemplateCollection extends Collection
{
    public function addItem($object, $key = null): void
    {
        if (get_class($object) != 'MyDramLibrary\View\HTMLTemplate') {
            throw new InvalidArgumentException('HTMLTemplate object expected');
        }
        parent::addItem($object, $key);
    }
}
