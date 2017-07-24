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
namespace DSchoenbauer\Orm\Events\Logger;

use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of LoggerClass
 *
 * @author David Schoenbauer
 */
class ErrorLogTest extends TestCase
{

    private $object;

    use TestModelTrait;

    protected function setUp()
    {
        $this->object = new ErrorLog();
    }

    public function testIsAbstractEvent()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->object);
    }

    public function testOnExecuteNoTarget()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteNoException()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($this->getModel());
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteGood()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $model = $this->getModel();
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        $event->expects($this->any())->method('getParam')->willReturnOnConsecutiveCalls(new \Exception('message'), "eventName");
        $data = [
            'success' => false,
            'event' => 'eventName',
            'message' => 'message',
        ];
        $this->assertTrue($this->object->onExecute($event));
        $this->assertEquals($data, $model->getData());
    }
}