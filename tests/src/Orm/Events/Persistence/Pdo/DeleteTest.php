<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Delete as DeleteCommand;
use DSchoenbauer\Sql\Exception\NoRecordsAffectedCreateException;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of PdoDeleteTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class DeleteTest extends TestCase
{

    private $object;
    private $mockAdapter;

    protected function setUp()
    {
        $this->mockAdapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = new Delete([], $this->mockAdapter);
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

    public function testDelete()
    {
        $mockDelete = $this->getMockBuilder(DeleteCommand::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($mockDelete, $this->object->setDelete($mockDelete)->getDelete());
    }

    public function testDeleteLazyLoad()
    {
        $this->assertInstanceOf(DeleteCommand::class, $this->object->getDelete());
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
        $idField = "id";

        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getTable')->willReturn($table);
        $entity->expects($this->any())->method('getIdField')->willReturn($idField);

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getEntity')->willReturn($entity);
        $model->expects($this->any())->method('getId')->willReturn(1);

        $delete = $this->getMockBuilder(DeleteCommand::class)->disableOriginalConstructor()->getMock();
        $delete->expects($this->any())->method('setIsStrict')->willReturnSelf();
        $delete->expects($this->any())->method('setTable')->with($table)->willReturnSelf();
        $delete->expects($this->any())->method('setWhere')->willReturnSelf();
        $delete->expects($this->any())->method('execute')->with($this->mockAdapter);


        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->assertTrue($this->object->setDelete($delete)->onExecute($event));
    }
    
    public function testOnExecuteNoRecord()
    {
        $table = "someTable";
        $idField = "id";
        
        $this->expectException(\DSchoenbauer\Orm\Exception\RecordNotFoundException::class);
        
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getTable')->willReturn($table);
        $entity->expects($this->any())->method('getIdField')->willReturn($idField);

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getEntity')->willReturn($entity);
        $model->expects($this->any())->method('getId')->willReturn(1);

        $delete = $this->getMockBuilder(DeleteCommand::class)->disableOriginalConstructor()->getMock();
        $delete->expects($this->any())->method('setIsStrict')->willReturnSelf();
        $delete->expects($this->any())->method('setTable')->with($table)->willReturnSelf();
        $delete->expects($this->any())->method('setWhere')->willReturnSelf();
        $delete->expects($this->any())->method('execute')->with($this->mockAdapter)->willThrowException(new NoRecordsAffectedCreateException());


        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->object->setDelete($delete)->onExecute($event);
    }
}
