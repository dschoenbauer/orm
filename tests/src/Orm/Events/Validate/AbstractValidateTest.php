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
namespace DSchoenbauer\Orm\Events\Validate;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Entity\HasBoolFieldsInterface;
use DSchoenbauer\Orm\Events\Validate\AbstractValidate;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Tests\Orm\Entity\AbstractEntityWithBool;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of AbstractValidateTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class AbstractValidateTest extends TestCase
{

    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(AbstractValidate::class);
    }

    public function testExecuteNotAModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->once())->method('getTarget');
        $this->assertNull($this->object->onExecute($event));
    }

    public function testExecuteDoesNotHaveAnInterface()
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->exactly(1))->method('getEntity')->willReturn($entity);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($this->object->onExecute($event));
    }

    public function testExecutePreFlightCheckFail()
    {
        $mock = $this->getMockBuilder(AbstractValidate::class)
            ->disableOriginalConstructor()
            ->setMethods(['preExecuteCheck'])
            ->getMockForAbstractClass();

        $mock->expects($this->exactly(1))->method('preExecuteCheck')->willReturn(false);
        $mock->expects($this->once())->method('getTypeInterface')->willReturn(HasBoolFieldsInterface::class);

        $entity = $this->getMockBuilder(AbstractEntityWithBool::class)->getMock();

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->exactly(1))->method('getEntity')->willReturn($entity);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($mock->onExecute($event));

    }

    public function testExecuteBasicData()
    {
        $this->object->expects($this->once())->method('getTypeInterface')->willReturn(HasBoolFieldsInterface::class);
        $this->object->expects($this->once())->method('getFields')->willReturn([]);

        $entity = $this->getMockBuilder(AbstractEntityWithBool::class)->getMock();

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->exactly(1))->method('getEntity')->willReturn($entity);
        $model->expects($this->exactly(1))->method('getData')->willReturn([]);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($this->object->onExecute($event));
    }

    public function testValidateIsGettingAllFields()
    {
        $data = ['id' => 1, 'values' => [1, 2, 3]];
        $params = ['id' => 100, 'values' => [1, 2, 3, 5]];
        $fields = ['id', 'values'];
        $this->object->expects($this->once())->method('getTypeInterface')->willReturn(HasBoolFieldsInterface::class);
        $this->object->expects($this->once())->method('getFields')->willReturn($fields);

        $entity = $this->getMockBuilder(AbstractEntityWithBool::class)->getMock();

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->exactly(1))->method('getEntity')->willReturn($entity);
        $model->expects($this->exactly(1))->method('getData')->willReturn($data);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);
        $event->expects($this->exactly(1))->method('getParams')->willReturn($params);

        $this->object->expects($this->once())->method('validate')->with($data, $fields);
        $this->assertNull($this->object->onExecute($event));
        $this->assertSame($model, $this->object->getModel());
        $this->assertSame($params, $this->object->getParams());
    }

    public function testModel()
    {
        $mockModel = $this->getMockBuilder(ModelInterface::class)->getMock();
        $this->assertSame($mockModel, $this->object->setModel($mockModel)->getModel());
    }

    public function testParam()
    {
        $param = ['test' => 1];
        $this->assertSame($param, $this->object->setParams($param)->getParams());
    }
}
