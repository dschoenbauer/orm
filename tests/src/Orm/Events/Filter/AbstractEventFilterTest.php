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
namespace DSchoenbauer\Orm\Events\Filter;

use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of AbstractEventFilterTest
 *
 * @author David Schoenbauer
 */
class AbstractEventFilterTest extends TestCase
{

    protected $object;

    use TestModelTrait;

    protected function setUp()
    {
        $this->setObject($this->getMockForAbstractClass(AbstractEventFilter::class));
    }

    public function testEvent()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertSame($event, $this->getObject()->setEvent($event)->getEvent());
    }

    public function testModel()
    {
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $this->assertSame($model, $this->getObject()->setModel($model)->getModel());
    }

    public function testOnExecuteBadModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->getObject()->onExecute($event));
        $this->assertSame($event, $this->getObject()->getEvent());
    }

    public function testOnExecuteGoodModel()
    {
        $this->object->expects($this->once())->method('filter')->with([])->willReturn(['id' => 1]);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->once())->method('getData')->willReturn([]);
        $model->expects($this->once())->method('setData')->with(['id' => 1]);

        $event->expects($this->exactly(2))->method('getTarget')->willReturn($model);

        $this->assertTrue($this->getObject()->onExecute($event));
        $this->assertSame($event, $this->getObject()->getEvent());
        $this->assertSame($model, $this->getObject()->getModel());
    }

    /**
     * Abstracts have proven hard to listen to with respect to data typing
     * @return AbstractEventFilter
     */
    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }
}
