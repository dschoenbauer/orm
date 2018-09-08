<?php
/*
 * The MIT License
 *
 * Copyright 2018 David Schoenbauer.
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

use Exception;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of ThrowExceptionEventTest
 *
 * @author David Schoenbauer
 */
class ThrowExceptionEventTest extends TestCase
{
    private $object;
    
    protected function setUp()
    {
        $this->object = new ThrowExceptionEvent();
    }
    
    function testOnExecuteGoodParameter(){
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $exception = new Exception('Test');
        $event->expects($this->any())->method('getParam')->willReturn($exception);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test');
        $this->object->onExecute($event);
    }
    
    function testOnExecuteBadParameter(){
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $exception = new Exception('Test');
        $this->expectException(Exception::class);
        $event->expects($this->any())->method('getParam')->willReturn(new \stdClass());
        $this->expectExceptionMessage(ThrowExceptionEvent::NO_EXCEPTION_MESSAGE);
        $this->object->onExecute($event);
    }
    
    function testOnExecuteNoParameter(){
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $exception = new Exception('Test');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(ThrowExceptionEvent::NO_EXCEPTION_MESSAGE);
        $this->object->onExecute($event);
    }
    
    function testAlwaysThrowException(){
        $this->assertFalse($this->object->getAlwaysThrowException());
        $this->assertTrue($this->object->setAlwaysThrowException()->getAlwaysThrowException());
        $this->assertFalse($this->object->setAlwaysThrowException(false)->getAlwaysThrowException());
    }
    
    function testAlwaysThrowExceptionContructor(){
        $this->object = new ThrowExceptionEvent([], true);
        $this->assertTrue($this->object->getAlwaysThrowException());
    }
}
