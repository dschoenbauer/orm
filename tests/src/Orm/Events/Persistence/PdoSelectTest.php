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
namespace DSchoenbauer\Orm\Events\Persistence;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Events\Persistence\PdoSelect;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Select;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of PdoSelectTest
 *
 * @author David Schoenbauer
 */
class PdoSelectTest extends TestCase
{

    private $object;
    private $mockAdapter;

    protected function setUp()
    {
        $this->mockAdapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = new PdoSelect([], $this->mockAdapter);
    }

    public function testAdapterFromContructor()
    {
        $this->assertSame($this->mockAdapter, $this->object->getAdapter());
    }

    public function testAdapter()
    {
        $mockAdapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($mockAdapter, $this->object->setAdapter($mockAdapter)->getAdapter());
    }

    public function testSelectConstructor()
    {
        $mockSelect = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $subject = new PdoSelect([], $this->mockAdapter, 0, $mockSelect);
        $this->assertSame($mockSelect, $subject->getSelect());
    }

    public function testSelect()
    {
        $mockSelect = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($mockSelect, $this->object->setSelect($mockSelect)->getSelect());
    }

    public function testSelectLazyLoad()
    {
        $this->assertInstanceOf(Select::class, $this->object->getSelect());
    }

    public function testOnExecuteTargetNotModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->once())
            ->method('getTarget')
            ->willReturn(null);
        $this->assertNull($this->object->onExecute($event));
    }

    public function testOnExecute()
    {
        $table = "someTable";
        $fields = ["some", "fields"];
        $idField = "id";

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->once())->method('getId')->willReturn(1);

        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->once())->method('getTable')->willReturn($table);
        $entity->expects($this->once())->method('getAllFields')->willReturn($fields);
        $entity->expects($this->once())->method('getIdField')->willReturn($idField);

        $select = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $select->expects($this->once())->method('setTable')->with($table)->willReturnSelf();
        $select->expects($this->once())->method('setFields')->with($fields)->willReturnSelf();
        $select->expects($this->once())->method('setWhere')->willReturnSelf();
        $select->expects($this->once())->method('setFetchFlat')->willReturnSelf();
        $select->expects($this->once())->method('execute')->with($this->mockAdapter);


        $model->expects($this->once())->method('getEntity')->willReturn($entity);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($this->object->setSelect($select)->onExecute($event));
    }
}
