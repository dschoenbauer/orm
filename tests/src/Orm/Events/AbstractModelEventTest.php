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
namespace DSchoenbauer\Orm\Events;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of AbstractEventTest
 *
 * @author David Schoenbauer
 */
class AbstractModelEventTest extends TestCase
{

    use TestModelTrait;

    /* @var $object AbstractModelEvent */
    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(AbstractModelEvent::class);
    }

    
    public function testGetInterface(){
        $this->assertEquals(EntityInterface::class, $this->object->getInterface());
    }
    
    public function testOnExecuteBadTarget(){
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->once())->method('getTarget')->willReturn(new \stdClass());
        $this->assertFalse($this->object->onExecute($event));
        $this->assertSame($event, $this->object->getEvent());
    }
    
    public function testOnExecuteGoodTargetBadEntity(){
        $model = $this->getModel(1, []);
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->once())->method('getTarget')->willReturn($model);
        $this->assertFalse($this->object->onExecute($event));
        $this->assertSame($event, $this->object->getEvent());
    }
    
    public function testOnExecuteGoodTargetGoodEntity(){
        $model = $this->getModel(1, [], $this->getMockBuilder(EntityInterface::class)->getMock());
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->once())->method('getTarget')->willReturn($model);
        $this->object->expects($this->once())->method('execute')->with($model)->willReturn(true);
        $this->assertTrue($this->object->onExecute($event));
        $this->assertSame($event, $this->object->getEvent());
    }

            

    public function testEvent(){
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertSame($event,$this->object->setEvent($event)->getEvent());
    }

}
