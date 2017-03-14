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
namespace DSchoenbauer\Orm\Events\Validate\DataType;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Model;
use DSchoenbauer\Tests\Orm\Entity\AbstractEntityWithBool;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\Event;

/**
 * Description of ValidateBoolean
 *
 * @author David Schoenbauer
 */
class ValidateBooleanTest extends PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new ValidateBoolean();
    }

    public function testExecuteNotAModel()
    {
        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->once())->method('getTarget');
        $this->assertNull($this->object->onExecute($event));
    }

    public function testExecuteDoesNotHaveBool()
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();

        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->exactly(1))->method('getEntity')->willReturn($entity);

        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($this->object->onExecute($event));
    }

    public function testExecuteBasicData()
    {
        $entity = $this->getMockBuilder(AbstractEntityWithBool::class)->getMock();
        $entity->expects($this->exactly(1))->method('getBoolFields')->willReturn([]);

        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->exactly(1))->method('getEntity')->willReturn($entity);
        $model->expects($this->exactly(1))->method('getData')->willReturn([]);

        $event = $this->getMockBuilder(Event::class)->getMock();
        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertNull($this->object->onExecute($event));
    }

    public function testValidateBoolNoError()
    {
        $data = [
            'boolFalse' => false,
            'boolTrue' => true,
            'number' => 1,
            'letter' => "A",
        ];
        $fields = [
            'boolFalse',
            'boolTrue'
        ];
        $this->assertNull($this->object->validateBool($data, $fields));
    }
    
    public function testValidateBoolNoErrorEdgeCase()
    {
        $data = [
            'boolFalse' => false,
            'boolTrue' => true,
            'numberOt' => 1,
            'letterAny' => "A",
            'null' => null,
        ];
        $fields = [
            'boolFalse',
            'boolTrue',
            'leterAny',
        ];
        $this->assertNull($this->object->validateBool($data, $fields));
    }
    public function testValidateBoolErrorEdgeCase()
    {
        $data = [
            'numberOt' => 1,
        ];
        $fields = [
            'numberOt',
        ];
        $this->expectException(\DSchoenbauer\Orm\Exception\InvalidDataTypeException::class);
        $this->object->validateBool($data, $fields);
    }
}
