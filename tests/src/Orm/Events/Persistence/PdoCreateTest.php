<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Model;
use DSchoenbauer\Sql\Command\Create;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of PdoCreateTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class PdoCreateTest extends TestCase
{

    private $object;
    private $mockAdapter;

    protected function setUp()
    {
        $this->mockAdapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = new PdoCreate([], $this->mockAdapter);
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

    public function testCreateConstructor()
    {
        $mockCreate = $this->getMockBuilder(Create::class)->disableOriginalConstructor()->getMock();
        $subject = new PdoCreate([], $this->mockAdapter, $mockCreate);
        $this->assertSame($mockCreate, $subject->getCreate());
    }

    public function testCreate()
    {
        $mockCreate = $this->getMockBuilder(Create::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($mockCreate, $this->object->setCreate($mockCreate)->getCreate());
    }

    public function testCreateLazyLoad()
    {
        $this->assertInstanceOf(Create::class, $this->object->getCreate());
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
        $data = ['test' => 1, 'some_id' => 2];

        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->once())->method('getTable')->willReturn($table);

        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->once())->method('getEntity')->willReturn($entity);
        $model->expects($this->once())->method('getData')->willReturn($data);

        $create = $this->getMockBuilder(Create::class)->disableOriginalConstructor()->getMock();
        $create->expects($this->once())->method('setData')->with($data)->willReturnSelf();
        $create->expects($this->once())->method('setTable')->with($table)->willReturnSelf();
        $create->expects($this->once())->method('execute')->with($this->mockAdapter);


        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($this->object->setCreate($create)->onExecute($event));
    }
}
