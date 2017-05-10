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
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of CreateTest
 *
 * @author David Schoenbauer
 */
class CreateTest extends TestCase
{

    const TEST_PATH = './files/';
    const TABLE_NAME = 'create';

    use TestModelTrait;

    protected $object;

    protected function setUp()
    {
        $this->object = new Create();
    }

    protected function tearDown()
    {
        @unlink(self::TEST_PATH . self::TABLE_NAME . '.json');
    }

    public function testHasProperParent()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->object);
    }

    public function testEventsDefaultEmptyPassed()
    {
        $this->assertEquals([], $this->object->getEvents());
    }

    public function testEventsDefaultPriority()
    {
        $this->assertEquals(EventPriorities::ON_TIME, $this->object->getPriority());
    }

    public function testEventsDefaultPath()
    {
        $this->assertEquals('.' . DIRECTORY_SEPARATOR, $this->object->getPath());
    }

    public function testNoDefaultValues()
    {
        $object = new Create(['test'], -10, '../.');
        $this->assertEquals(['test'], $object->getEvents());
        $this->assertEquals(-10, $object->getPriority());
        $this->assertEquals('..' . DIRECTORY_SEPARATOR . '.' . DIRECTORY_SEPARATOR, $object->getPath());
    }

    public function testOnExecuteNoModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteModelNoEntity()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($this->getModel(0, [], null));
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteFullLoadArray()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $entity = $this->getAbstractEntity('id', self::TABLE_NAME);
        $model = $this->getModel(null, ['row' => 1], $entity);
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        $this->assertTrue($this->object->setPath(self::TEST_PATH)->onExecute($event));
        $this->assertEquals(['id' => 0, 'row' => 1], $model->getData(), 'Model Data');
        $this->assertEquals(0, $model->getId(), 'Id');
    }

    public function testOnExecuteFullLoadScalar()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $entity = $this->getAbstractEntity('id', self::TABLE_NAME);
        $model = $this->getModel(null, 'two', $entity);
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        $this->assertTrue($this->object->setPath(self::TEST_PATH)->onExecute($event));
        $this->assertEquals("two", $model->getData(), 'Model Data');
        $this->assertEquals(0, $model->getId(), 'Id');
    }

    public function testAddData()
    {
        $data = [];
        $entity = $this->getAbstractEntity('id');
        $model = $this->getModel(null, ['row' => 1], $entity);
        $this->assertEquals([0 => ['id' => 0, 'row' => 1]], $this->object->addData($data, $model), 'Add Data');
        $this->assertEquals(['id' => 0, 'row' => 1], $model->getData(), 'Model Data');
        $this->assertEquals(0, $model->getId(), 'Id');
    }

    public function testGetId()
    {
        $data = [1, 2, 3, 4];
        $this->assertEquals(3, $this->object->getId($data));
    }

    public function testGetIdAdd()
    {
        $data = [1, 2, 3, 4];
        $data[] = 5;
        $this->assertEquals(4, $this->object->getId($data));
    }

    public function testGetIdRemove()
    {
        $data = [1, 2, 3, 4];
        unset($data[2]);
        $this->assertEquals(3, $this->object->getId($data));
    }

    public function getEntity($idField = 'id', $tableName = 'test')
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getIdField')->willReturn($idField);
        $entity->expects($this->any())->method('getTable')->willReturn($tableName);
        return $entity;
    }
}
