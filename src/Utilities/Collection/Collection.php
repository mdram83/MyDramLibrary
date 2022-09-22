<?php

namespace MyDramLibrary\Utilities\Collection;

use IteratorAggregate;
use Traversable;

class Collection implements IteratorAggregate
{
    protected array $members = array();
    protected array|string|null $onload = null;
    protected bool $loaded = false;

    public function getIterator(): Traversable
    {
        $this->checkCallback();
        return new CollectionIterator(clone $this);
    }

    public function addItem($object, $key = null): void
    {
        $this->checkCallback();
        if (isset($key)) {
            if (isset($this->members[$key])) {
                throw new CollectionKeyInUseException("Key $key already in use");
            } else {
                $this->addItemWithKey($object, $key);
            }
        } else {
            $this->addItemWithoutKey($object);
        }
    }

    protected function addItemWithKey($object, $key): void
    {
        $this->members[$key] = $object;
    }

    protected function addItemWithoutKey($object): void
    {
        $this->members[] = $object;
    }

    public function removeItem($key): void
    {
        $this->checkCallback();
        if (isset($this->members[$key])) {
            unset($this->members[$key]);
        } else {
            throw new CollectionKeyInvalidException("Key $key not set");
        }
    }

    public function getItem($key)
    {
        $this->checkCallback();
        if (isset($this->members[$key])) {
            return $this->members[$key];
        } else {
            throw new CollectionKeyInvalidException("Key $key not set");
        }
    }

    public function keys(): array
    {
        $this->checkCallback();
        return array_keys($this->members);
    }

    public function length(): int
    {
        $this->checkCallback();
        return count($this->members);
    }

    public function exists($key): bool
    {
        $this->checkCallback();
        return isset($this->members[$key]);
    }

    public function setLoadCallback(string $functionName, object $objectOrClass = null): void
    {
        if (isset($objectOrClass)) {
            $callback = array($objectOrClass, $functionName);
        } else {
            $callback = $functionName;
        }

        if (!is_callable($callback)) {
            throw new CollectionException('Incorrect callback function');
        } else {
            $this->onload = $callback;
        }
    }

    protected function checkCallback(): void
    {
        if (isset($this->onload) && !$this->loaded) {
            $this->loaded = true;
            call_user_func($this->onload, $this);
        }
    }
}
