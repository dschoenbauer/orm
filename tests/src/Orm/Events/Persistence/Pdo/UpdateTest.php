<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Update as UpdateCommand;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of PdoUpdateTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class UpdateTest extends TestCase
{

    private $object;
    private $mockAdapter;

    protected function setUp()
    {
        $this->mockAdapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = new Update([], $this->mockAdapter);
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

    public function testUpdate()
    {
        $mockUpdate = $this->getMockBuilder(UpdateCommand::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($mockUpdate, $this->object->setUpdate($mockUpdate)->getUpdate());
    }

    public function testUpdateLazyLoad()
    {
        $this->assertInstanceOf(UpdateCommand::class, $this->object->getUpdate());
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
        $data = ['name' => "some", 'street' => "fields"];
        $idField = "id";

        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->once())->method('getTable')->willReturn($table);
        $entity->expects($this->once())->method('getIdField')->willReturn($idField);

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->once())->method('getEntity')->willReturn($entity);
        $model->expects($this->once())->method('getId')->willReturn(1);
        $model->expects($this->once())->method('getData')->willReturn($data);

        $select = $this->getMockBuilder(UpdateCommand::class)->disableOriginalConstructor()->getMock();
        $select->expects($this->once())->method('setTable')->with($table)->willReturnSelf();
        $select->expects($this->once())->method('setData')->with($data)->willReturnSelf();
        $select->expects($this->once())->method('setWhere')->willReturnSelf();
        $select->expects($this->once())->method('execute')->with($this->mockAdapter);


        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($this->object->setUpdate($select)->onExecute($event));
    }
}
