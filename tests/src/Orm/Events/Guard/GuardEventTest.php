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
namespace DSchoenbauer\Orm\Events\Guard;

use DSchoenbauer\Exception\Http\ClientError\ForbiddenException;
use DSchoenbauer\Orm\Events\Guard\GuardEvent;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of GuardEventTest
 *
 * @author David Schoenbauer
 */
class GuardEventTest extends TestCase
{
    /* @var $object GuardEvent */

    private $object;

    protected function setUp()
    {
        $this->object = new GuardEvent();
    }

    public function testAddGuards()
    {
        $guard = $this->getMockBuilder(GuardInterface::class)->getMock();
        $this->assertSame([$guard], $this->object->add($guard)->getGuards());
    }

    public function testConstruct()
    {
        $guard = $this->getMockBuilder(GuardInterface::class)->getMock();
        $guards = [$guard, $guard];
        $events = ['test'];
        $priority = 100;
        $object = new GuardEvent($events, $guards, $priority);
        
        $this->assertEquals($guards, $object->getGuards());
        $this->assertEquals($events, $object->getEvents());
        $this->assertEquals($priority, $object->getPriority());
    }
   
    public function testAuthenticateFalse(){
        $guard = $this->getMockBuilder(GuardInterface::class)->getMock();
        $guard->expects($this->once())->method('authenticate')->willReturn(false);
        $this->assertFalse($this->object->add($guard)->authenticate());
    }
    
    public function testAuthenticateFalseTwice(){
        $guard = $this->getMockBuilder(GuardInterface::class)->getMock();
        $guard->expects($this->exactly(2))->method('authenticate')->willReturn(false);
        $this->assertFalse($this->object->add($guard)->add($guard)->authenticate());
    }
    public function testAuthenticateTrue(){
        $guard = $this->getMockBuilder(GuardInterface::class)->getMock();
        $guard->expects($this->exactly(1))->method('authenticate')->willReturn(true);
        $this->assertTrue($this->object->add($guard)->authenticate());
    }
    
    public function testAuthenticateTrueNoMore(){
        $guard = $this->getMockBuilder(GuardInterface::class)->getMock();
        $guard->expects($this->exactly(1))->method('authenticate')->willReturn(true);

        $guardBad = $this->getMockBuilder(GuardInterface::class)->getMock();
        $guardBad->expects($this->exactly(0))->method('authenticate')->willReturn(false);

        $this->assertTrue($this->object->add($guard)->add($guardBad)->authenticate());
    }
    
    public function testOnExecuteFail(){
        $this->expectException(ForbiddenException::class);
        
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $guard = $this->getMockBuilder(GuardInterface::class)->getMock();
        $guard->expects($this->exactly(1))->method('authenticate')->willReturn(false);
        
        $this->object->add($guard)->onExecute($event);
    }
    public function testOnExecuteSuccess(){
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $guard = $this->getMockBuilder(GuardInterface::class)->getMock();
        $guard->expects($this->exactly(1))->method('authenticate')->willReturn(true);
        
        $this->assertTrue($this->object->add($guard)->onExecute($event));
    }
    
}
