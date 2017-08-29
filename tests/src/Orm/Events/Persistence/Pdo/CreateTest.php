<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Exception\RecordNotFoundException;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Create as CreateCommand;
use DSchoenbauer\Sql\Exception\NoRecordsAffectedCreateException;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of PdoCreateTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class CreateTest extends TestCase
{

    private $object;
    private $mockAdapter;

    protected function setUp()
    {
        $this->mockAdapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = new Create([], $this->mockAdapter);
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

    public function testCreate()
    {
        $mockCreate = $this->getMockBuilder(CreateCommand::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($mockCreate, $this->object->setCreate($mockCreate)->getCreate());
    }

    public function testCreateLazyLoad()
    {
        $this->assertInstanceOf(CreateCommand::class, $this->object->getCreate());
    }

    public function testOnExecuteTargetNotModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())
            ->method('getTarget')
            ->willReturn(null);
        $this->assertNull($this->object->onExecute($event));
    }

    public function testOnExecute()
    {
        $table = "someTable";
        $idField = "id";
        $data = ['test' => 1, 'some_id' => 2];

        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getTable')->willReturn($table);

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getEntity')->willReturn($entity);
        $model->expects($this->any())->method('getData')->willReturn($data);
        $model->expects($this->any())->method('setId')->with(1);

        $create = $this->getMockBuilder(CreateCommand::class)->disableOriginalConstructor()->getMock();
        $create->expects($this->any())->method('setIsStrict')->willReturnSelf();
        $create->expects($this->any())->method('setData')->with($data)->willReturnSelf();
        $create->expects($this->any())->method('setTable')->with($table)->willReturnSelf();
        $create->expects($this->any())->method('execute')->with($this->mockAdapter)->willReturn(1);


        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->assertTrue($this->object->setCreate($create)->onExecute($event));
    }

    public function testOnExecuteNoRecord()
    {
        $table = "someTable";
        $idField = "id";
        $data = ['test' => 1, 'some_id' => 2];

        $this->expectException(RecordNotFoundException::class);
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getTable')->willReturn($table);

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getEntity')->willReturn($entity);
        $model->expects($this->any())->method('getData')->willReturn($data);

        $create = $this->getMockBuilder(CreateCommand::class)->disableOriginalConstructor()->getMock();
        $create->expects($this->any())->method('setIsStrict')->willReturnSelf();
        $create->expects($this->any())->method('setData')->with($data)->willReturnSelf();
        $create->expects($this->any())->method('setTable')->with($table)->willReturnSelf();
        $create->expects($this->any())->method('execute')->with($this->mockAdapter)->willThrowException(new NoRecordsAffectedCreateException());


        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->object->setCreate($create)->onExecute($event);
    }
}
