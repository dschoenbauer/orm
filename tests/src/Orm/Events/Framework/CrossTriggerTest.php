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
namespace DSchoenbauer\Orm\Events\Framework;

use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;

/**
 * Description of CrossTrigger
 *
 * @author David Schoenbauer
 */
class CrossTriggerTest extends TestCase
{

    protected $object;
    use TestModelTrait;

    protected function setUp()
    {
        $this->object = new CrossTrigger();
    }

    public function testTargetEvents()
    {
        $targetEvents = ['bob', 'fran'];
        $this->assertEquals($targetEvents, $this->object->setTargetEvents($targetEvents)->getTargetEvents());
    }

    public function testTargetEventsFromConstructor()
    {
        $targetEvents = ['dan', 'fred'];
        $events = ['steve'];
        $this->object = new CrossTrigger(['steve'], $targetEvents, 1111);
        
        $this->assertEquals($targetEvents, $this->object->getTargetEvents());
        $this->assertEquals($events, $this->object->getEvents());
        $this->assertEquals(1111, $this->object->getPriority());
    }
    
    public function testOnExecuteNoModel(){
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }
    
    public function testOnExecuteModelGood(){
        $targetEvents = ['test', 'test2'];
        $eventManagerMock = $this->getMockBuilder(EventManager::class)->getMock();

        $model = $this->getModel();
        $model->expects($this->any())->method('getEventManager')->willReturn($eventManagerMock);
        $eventManagerMock
            ->expects($this->exactly(count($targetEvents)))
            ->method('trigger')
            ->withConsecutive(['test', $model],['test2', $model]);
        
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        
        $this->assertTrue($this->object->setTargetEvents($targetEvents)->onExecute($event));
    }
}
