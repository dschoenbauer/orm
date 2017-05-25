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
namespace DSchoenbauer\Orm\Events\Persistence\File;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Exception\RecordNotFoundException;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of SelectTest
 *
 * @author David Schoenbauer
 */
class SelectTest extends TestCase
{

    protected $object;
    private $testPath;

    use TestModelTrait;

    protected function setUp()
    {
        $this->testPath = str_replace('/', DIRECTORY_SEPARATOR, dirname(__FILE__) . '/../../../../../files/');
        $this->object = new Select();
    }

    public function testOnExecuteRecordNotFound()
    {
        $this->expectException(RecordNotFoundException::class);
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($this->getModel(0, [], $entity));
        $this->object->onExecute($event);
    }

    public function testOnExecuteRecordFound()
    {
        $entity = $this->getEntity('id', 'select');
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->object->setPath($this->testPath);
        $model = $this->getModel(0, [], $entity);
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        $this->assertTrue($this->object->onExecute($event));
        $this->assertEquals(['row' => 1, 'id' => 0], $model->getData());
    }

    public function testOnExecuteRecordFoundLarge()
    {
        $entity = $this->getEntity('id', 'select');
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->object->setPath($this->testPath);
        $model = $this->getModel(10, [], $entity);
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        $this->assertTrue($this->object->onExecute($event));
        $this->assertEquals(['row' => 11, 'id' => 10], $model->getData());
    }

    public function getEntity($idField = 'id', $tableName = 'test')
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getIdField')->willReturn($idField);
        $entity->expects($this->any())->method('getTable')->willReturn($tableName);
        return $entity;
    }
}
