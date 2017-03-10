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

use PHPUnit_Framework_TestCase;
use Zend\EventManager\EventManager;

/**
 * Description of CrudModelTest
 *
 * @author David Schoenbauer
 */
class CrudModelTest extends PHPUnit_Framework_TestCase {

    protected $object;
    protected $mockEventManager;
    private $entity;

    protected function setUp() {
        $this->entity = $this->getMockBuilder(Entity\EntityInterface::class)->getMock();
        $this->object = new CrudModel($this->entity);
        $this->mockEventManager = $this->getMockBuilder(EventManager::class)->getMock();
    }

    public function testCreate() {
        $this->mockEventManager->expects($this->exactly(3))
                ->method('trigger')
                ->withConsecutive(
                        [Enum\ModelEvents::VALIDATE, $this->object, ['events' => [Enum\ModelEvents::CREATE, Enum\ModelEvents::FETCH]]], [Enum\ModelEvents::CREATE, $this->object], [Enum\ModelEvents::FETCH, $this->object]);

        $this->object->setEventManager($this->mockEventManager);
        $data = ['test' => 'value'];
        $this->assertEquals($data, $this->object->create($data));
        $this->assertEquals($data, $this->object->getData());
    }

    public function testFetch() {
        $this->mockEventManager->expects($this->exactly(2))
                ->method('trigger')
                ->withConsecutive(
                        [Enum\ModelEvents::VALIDATE, $this->object, ['events' => [Enum\ModelEvents::FETCH]]], 
                        [Enum\ModelEvents::FETCH, $this->object]);

        $this->object->setEventManager($this->mockEventManager);
        $id = 1447;
        $data = ['test' => 'value'];
        $this->assertEquals($data, $this->object->setData($data)->fetch($id));
        $this->assertEquals($id, $this->object->getId());
    }

    public function testFetchAll() {
        $this->mockEventManager->expects($this->exactly(2))
                ->method('trigger')
                ->withConsecutive(
                        [Enum\ModelEvents::VALIDATE, $this->object, ['events' => [Enum\ModelEvents::FETCH_ALL]]], 
                        [Enum\ModelEvents::FETCH_ALL, $this->object]);

        $this->object->setEventManager($this->mockEventManager);
        $data = ['test' => 'value'];
        $this->assertEquals($data, $this->object->setData($data)->fetchAll());
    }
    
    public function testUpdate() {
        $this->mockEventManager->expects($this->exactly(3))
                ->method('trigger')
                ->withConsecutive(
                        [Enum\ModelEvents::VALIDATE, $this->object, ['events' => [Enum\ModelEvents::UPDATE, Enum\ModelEvents::FETCH]]], 
                        [Enum\ModelEvents::UPDATE, $this->object], 
                        [Enum\ModelEvents::FETCH, $this->object]
                        );

        $this->object->setEventManager($this->mockEventManager);
        $id = 1447;
        $data = ['test' => 'value'];
        $this->assertEquals($data, $this->object->update($id,$data));
        $this->assertEquals($data, $this->object->getData());
        $this->assertEquals($id, $this->object->getId());
    }
    
    public function testDelete() {
        $this->mockEventManager->expects($this->exactly(2))
                ->method('trigger')
                ->withConsecutive(
                        [Enum\ModelEvents::VALIDATE, $this->object, ['events' => [Enum\ModelEvents::DELETE]]], 
                        [Enum\ModelEvents::DELETE, $this->object]);

        $this->object->setEventManager($this->mockEventManager);
        $id = 1447;
        $this->assertTrue($this->object->delete($id));
        $this->assertEquals($id, $this->object->getId());
    }
}
