<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Model;
use DSchoenbauer\Sql\Command\Update;
use PDO;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\Event;

/**
 * Description of PdoUpdateTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class PdoUpdateTest extends PHPUnit_Framework_TestCase
{

    private $object;
    private $mockAdapter;

    protected function setUp()
    {
        $this->mockAdapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = new PdoUpdate([], $this->mockAdapter);
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

    public function testUpdateConstructor()
    {
        $mockUpdate = $this->getMockBuilder(Update::class)->disableOriginalConstructor()->getMock();
        $subject = new PdoUpdate([], $this->mockAdapter, $mockUpdate);
        $this->assertSame($mockUpdate, $subject->getUpdate());
    }

    public function testUpdate()
    {
        $mockUpdate = $this->getMockBuilder(Update::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($mockUpdate, $this->object->setUpdate($mockUpdate)->getUpdate());
    }

    public function testUpdateLazyLoad()
    {
        $this->assertInstanceOf(Update::class, $this->object->getUpdate());
    }

    public function testOnExecuteTargetNotModel()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
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

        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->once())->method('getEntity')->willReturn($entity);
        $model->expects($this->once())->method('getId')->willReturn(1);
        $model->expects($this->once())->method('getData')->willReturn($data);

        $select = $this->getMockBuilder(Update::class)->disableOriginalConstructor()->getMock();
        $select->expects($this->once())->method('setTable')->with($table)->willReturnSelf();
        $select->expects($this->once())->method('setData')->with($data)->willReturnSelf();
        $select->expects($this->once())->method('setWhere')->willReturnSelf();
        $select->expects($this->once())->method('execute')->with($this->mockAdapter);


        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($this->object->setUpdate($select)->onExecute($event));
    }
}
