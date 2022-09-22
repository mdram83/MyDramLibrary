<?php

use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Collection\Collection;
use MyDramLibrary\Utilities\Collection\CollectionIterator;

class CollectionIteratorTest extends TestCase {

    private Collection $testCollection;
    private stdClass $testObject1;
    private stdClass $testObject2;
    private array $objectsArray;

    final public function setUp() : void {
        $this->testObject1 = new stdClass();
        $this->testObject2 = new stdClass();
        $testObject3 = new stdClass();

        $this->testCollection = new Collection();

        $this->objectsArray = [
            0 => $this->testObject1,
            1 => $this->testObject2,
            'testKey' => $testObject3,
        ];

        foreach ($this->objectsArray as $key => $value) {
            $this->testCollection->addItem($value, $key);
        }
    }
    
    final public function tearDown() : void {
        unset($this->testCollection);
        unset($this->objectsArray);
    }

    public function testCollectionIteratorImplementsIterator() {
        $this->assertInstanceof('Iterator', new CollectionIterator(new Collection()));
    }

    public function testReturnCurrentObject() {
        $collectionIterator = new CollectionIterator($this->testCollection);
        $this->assertSame($this->testObject1, $collectionIterator->current());
    }

    public function testReturnCurrentKey() {
        $collectionIterator = new CollectionIterator($this->testCollection);
        $this->assertSame(0, $collectionIterator->key());
    }

    public function testReturnCurrentObjectAfterIncreasingIndex() {
        $collectionIterator = new CollectionIterator($this->testCollection);
        $collectionIterator->next();
        $this->assertSame($this->testObject2, $collectionIterator->current());
    }

    public function testReturnFirstObjectAfterRewind() {
        $collectionIterator = new CollectionIterator($this->testCollection);
        $collectionIterator->next();
        $collectionIterator->rewind();
        $this->assertSame($this->testObject1, $collectionIterator->current());
    }

    public function testReturnCorrectNumberOfObjectsInForeach() {
        $collectionIterator = new CollectionIterator($this->testCollection);
        $checkLength = 0;
        foreach($collectionIterator as $ignored) {
            $checkLength++;
        }
        $this->assertSame($this->testCollection->length(), $checkLength);
    }

    public function testReturnSameObjectsAndKeysInForeach() {
        $collectionIterator = new CollectionIterator($this->testCollection);
        $checkArray = array();
        foreach($collectionIterator as $key => $value) {
            $checkArray[$key] = $value;
        }
        $this->assertSame($this->objectsArray, $checkArray);
    }

}