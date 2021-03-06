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
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Events\Persistence\Pdo\Select;
use DSchoenbauer\Orm\Exception\RecordNotFoundException;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Select as SelectCommand;
use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of PdoSelectTest
 *
 * @author David Schoenbauer
 */
class SelectTest extends TestCase
{

    private $object;
    private $mockAdapter;

    protected function setUp()
    {
        $this->mockAdapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = new Select([], $this->mockAdapter);
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

    public function testSelect()
    {
        $mockSelect = $this->getMockBuilder(SelectCommand::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($mockSelect, $this->object->setSelect($mockSelect)->getSelect());
    }

    public function testSelectLazyLoad()
    {
        $this->assertInstanceOf(SelectCommand::class, $this->object->getSelect());
    }

    public function testOnExecuteTargetNotModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())
            ->method('getTarget')
            ->willReturn(null);
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecute()
    {
        $table = "someTable";
        $fields = ["some", "fields"];
        $idField = "id";

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getId')->willReturn(1);

        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getTable')->willReturn($table);
        $entity->expects($this->any())->method('getAllFields')->willReturn($fields);
        $entity->expects($this->any())->method('getIdField')->willReturn($idField);

        $select = $this->getMockBuilder(SelectCommand::class)->disableOriginalConstructor()->getMock();
        $select->expects($this->any())->method('setIsStrict')->willReturnSelf();
        $select->expects($this->any())->method('setTable')->with($table)->willReturnSelf();
        $select->expects($this->any())->method('setFields')->with($fields)->willReturnSelf();
        $select->expects($this->any())->method('setWhere')->willReturnSelf();
        $select->expects($this->any())->method('setFetchFlat')->willReturnSelf();
        $select->expects($this->any())->method('execute')->with($this->mockAdapter);


        $model->expects($this->any())->method('getEntity')->willReturn($entity);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->assertTrue($this->object->setSelect($select)->onExecute($event));
    }
    
    public function testOnExecuteNoRecord()
    {
        $table = "someTable";
        $fields = ["some", "fields"];
        $idField = "id";

        $this->expectException(RecordNotFoundException::class);
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getId')->willReturn(1);

        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getTable')->willReturn($table);
        $entity->expects($this->any())->method('getAllFields')->willReturn($fields);
        $entity->expects($this->any())->method('getIdField')->willReturn($idField);

        $select = $this->getMockBuilder(SelectCommand::class)->disableOriginalConstructor()->getMock();
        $select->expects($this->any())->method('setIsStrict')->willReturnSelf();
        $select->expects($this->any())->method('setTable')->with($table)->willReturnSelf();
        $select->expects($this->any())->method('setFields')->with($fields)->willReturnSelf();
        $select->expects($this->any())->method('setWhere')->willReturnSelf();
        $select->expects($this->any())->method('setFetchFlat')->willReturnSelf();
        $select->expects($this->any())->method('execute')->with($this->mockAdapter)->willThrowException(new NoRecordsAffectedException());


        $model->expects($this->any())->method('getEntity')->willReturn($entity);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->object->setSelect($select)->onExecute($event);
    }
}
