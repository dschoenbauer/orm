<?php

/*
 * The MIT License
 *
 * Copyright 2017 David Schoenbauer.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace DSchoenbauer\Orm;

use DSchoenbauer\Orm\Framework\AttributeCollection;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\EventManager;

/**
 * Description of ModelTest
 *
 * @author David Schoenbauer
 */
class ModelTest extends PHPUnit_Framework_TestCase {

    private $object;
    private $entity;

    protected function setUp() {
        $this->entity = $this->getMockEntity();
        $this->object = new Model($this->entity);
    }

    private function getMockEntity() {
        return $this->getMockBuilder(Entity\EntityInterface::class)->getMock();
    }

    public function testEntityThroughConstructor() {
        $this->assertSame($this->entity, $this->object->getEntity());
    }

    public function testEntity() {
        $entity = $this->getMockEntity();
        $this->assertSame($entity, $this->object->setEntity($entity)->getEntity());
    }

    public function testId() {
        $this->assertEquals(10, $this->object->setId(10)->getId());
    }

    public function testData() {
        $this->assertEquals('test', $this->object->setData('test')->getData());
        $this->assertEquals(['test'], $this->object->setData(['test'])->getData());
    }

    public function testAccept() {
        $mock = $this->getMockBuilder(VisitorInterface::class)->getMock();
        $mock->expects($this->once())->method('visitModel')->willReturnCallback(function($arg) {
            $this->assertEquals($this->object, $arg);
        });

        $this->assertEquals($this->object, $this->object->accept($mock));
    }

    public function testEventManager() {
        $this->assertInstanceOf(EventManager::class, $this->object->getEventManager());
    }

    public function testAttributeCollection() {
        $this->assertInstanceOf(AttributeCollection::class, $this->object->getAttributes());
    }

}
