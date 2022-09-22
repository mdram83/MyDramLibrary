<?php

namespace MyDramLibrary\Utilities\Collection;

use Iterator;

class CollectionIterator implements Iterator
{
    private Collection $collection;
    private array $keys;
    private int $currentIndex = 0;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        $this->keys = $this->collection->keys();
    }

    public function current(): mixed
    {
        return $this->collection->getItem($this->keys[$this->currentIndex]);
    }

    public function key(): mixed
    {
        return $this->keys[$this->currentIndex];
    }

    public function next(): void
    {
        $this->currentIndex++;
    }

    public function rewind(): void
    {
        $this->currentIndex = 0;
    }

    public function valid(): bool
    {
        return $this->currentIndex < $this->collection->length();
    }
}
