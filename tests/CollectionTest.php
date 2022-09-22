<?php

use PHPUnit\Framework\TestCase;
use MyDramLibrary\Utilities\Collection\Collection;
use MyDramLibrary\Utilities\Validator\Validator;

class CollectionTest extends TestCase {

    public function CollectionTestMethod(Collection $collection) : void { }

    public function testCollectionCreated() {
        $this->assertInstanceOf('MyDramLibrary\Utilities\Collection\Collection', new Collection());
    }

    public function testThrowCollectionKeyInUseExceptionWhenAddingExistingKey() {
        $this->expectException('MyDramLibrary\Utilities\Collection\CollectionKeyInUseException');
        $collection = new Collection();
        $collection->addItem('test1', 1);
        $collection->addItem('test2', 1);
    }

    public function testThrowCollectionKeyInvalidExceptionWhenRemovingNotExistingKey() {
        $this->expectException('MyDramLibrary\Utilities\Collection\CollectionKeyInvalidException');
        $collection = new Collection();
        $collection->removeItem(1);
    }

    public function testThrowCollectionKeyInvalidExceptionWhenGettingNotExistingKey() {
        $this->expectException('MyDramLibrary\Utilities\Collection\CollectionKeyInvalidException');
        $collection = new Collection();
        $collection->getItem(1);
    }

    public function testReturnObjectAddedToCollection() {
        $collection = new Collection();
        $object = new stdClass();
        $collection->addItem($object, 1);
        $this->assertSame($object, $collection->getItem(1));
    }

    public function testReturnKeysOfCollection() {
        $testArray = [
            1 => new stdClass(),
            2 => new stdClass(),
            'third' => new stdClass(),
        ];
        $collection = new Collection();
        foreach ($testArray as $key => $value) {
            $collection->addItem($value, $key);
        }
        $this->assertEquals(array_keys($testArray), $collection->keys());
    }

    public function testReturnZeroWhenCheckingLengthOfEmptyCollection() {
        $collection = new Collection();
        $this->assertTrue(0 === $collection->length());
    }

    public function testReturnLengthOfPopulatedCollection() {
        $testArray = [
            1 => new stdClass(),
            2 => new stdClass(),
            'third' => new stdClass(),
        ];
        $collection = new Collection();
        foreach ($testArray as $key => $value) {
            $collection->addItem($value, $key);
        }
        $this->assertEquals(count($testArray), $collection->length());
    }

    public function testReturnTrueWhenCheckingForExistingKey() {
        $collection = new Collection();
        $key = 1;
        $collection->addItem(new stdClass(), $key);
        $this->assertTrue($collection->exists($key));
    }

    public function testReturnFalseWhenCheckingForMissingKey() {
        $collection = new Collection();
        $key = 1;
        $collection->addItem(new stdClass(), $key);
        $this->assertFalse($collection->exists($key + 1));
    }

    public function testSetAndUseCallbackFunctionOnLoad() {
        $collection = new Collection();
        $collection->setLoadCallback('get_class');
        $collection->length();
        $this->addToAssertionCount(1);
    }

    public function testThrowExceptionWhenSettingNotExistingFunctionToCallback() {
        $this->expectException('MyDramLibrary\Utilities\Collection\CollectionException');
        $collection = new Collection();
        $collection->setLoadCallback('get_classsssss');
    }

    public function testSetAndUseCallbackMethodOnLoad() {
        $collection = new Collection();
        $collection->setLoadCallback('CollectionTestMethod', $this);
        $collection->length();
        $this->addToAssertionCount(1);
    }

    public function testThrowsExceptionWhenSettingNotExistingMethodToCallback() {
        $this->expectException('MyDramLibrary\Utilities\Collection\CollectionException');
        $collection = new Collection();
        $collection->setLoadCallback('NotExistingMethodddddd', $this);
    }

    public function testAllowStaticMethodToBeSetAsCollectionCallbackFunctionButThrowsTypeError() {
        $this->expectException('TypeError');
        $collection = new Collection();
        $collection->setLoadCallback('isNumberBetween', new Validator());
        $collection->length();
    }

    public function testCollectionIsTraversableClass() {
        $this->assertInstanceOf('Traversable', new Collection());
    }

    public function testIterateThroughCollection() {
        $testArray = [
            1 => new stdClass(),
            2 => new stdClass(),
            'third' => new stdClass(),
        ];
        $collection = new Collection();
        foreach ($testArray as $key => $value) {
            $collection->addItem($value, $key);
        }

        $checkArray = array();
        foreach ($collection as $key => $value) {
            $checkArray[$key] = $value;
        }

        $this->assertSame($testArray, $checkArray);
        
    }

}