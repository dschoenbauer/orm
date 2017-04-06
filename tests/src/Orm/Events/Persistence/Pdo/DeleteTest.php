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
        $event->expects($this->once())
            ->method('getTarget')
            ->willReturn(null);
        $this->assertNull($this->object->onExecute($event));
    }

    public function testOnExecute()
    {
        $table = "someTable";
        $idField = "id";

        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->once())->method('getTable')->willReturn($table);
        $entity->expects($this->once())->method('getIdField')->willReturn($idField);

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->once())->method('getEntity')->willReturn($entity);
        $model->expects($this->once())->method('getId')->willReturn(1);

        $delete = $this->getMockBuilder(DeleteCommand::class)->disableOriginalConstructor()->getMock();
        $delete->expects($this->once())->method('setTable')->with($table)->willReturnSelf();
        $delete->expects($this->once())->method('setWhere')->willReturnSelf();
        $delete->expects($this->once())->method('execute')->with($this->mockAdapter);


        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($this->object->setDelete($delete)->onExecute($event));
    }
}
