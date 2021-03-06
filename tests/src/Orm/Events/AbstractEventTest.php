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

use DSchoenbauer\Exception\Platform\InvalidArgumentException;
use DSchoenbauer\Exception\Platform\LogicException;
use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\EventManager\EventManagerInterface;

/**
 * Description of AbstractEventTest
 *
 * @author David Schoenbauer
 */
class AbstractEventTest extends TestCase
{

    use TestModelTrait;

    /* @var $object AbstractEvent */

    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(AbstractEvent::class);
    }

    public function testEvents()
    {
        $events = ['onTest', 'onTest2'];
        $this->assertEquals($events, $this->object->setEvents($events)->getEvents());
    }

    public function testVisitModel()
    {
        $eventManagerMock = $this->getMockBuilder(EventManagerInterface::class)->getMock();
        $eventManagerMock->expects($this->once())
            ->method('attach')
            ->with('test', [$this->object, 'onExecute'], 1);
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())
            ->method('accept')
            ->willReturnCallback(function(VisitorInterface $event) use ($model) {
                $event->visitModel($model);
            });
        $model->expects($this->once())
            ->method('getEventManager')
            ->willReturn($eventManagerMock);
        $this->assertInstanceOf(VisitorInterface::class, $this->object);
        $model->accept($this->object->setEvents(['test']));
    }

    public function testDefaultPriority()
    {
        $this->assertEquals(EventPriorities::ON_TIME, $this->object->getPriority());
    }

    public function testPriority()
    {
        $this->assertEquals(10000, $this->object->setPriority(10000)->getPriority());
    }

    public function testValidateModelModelBadException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("ModelInterface is expected");
        $this->object->validateModel(new stdClass(), "notAnEntity", true);
    }

    public function testValidateModelModelBadBoolean()
    {
        $this->assertFalse($this->object->validateModel(new stdClass(), "notanentity"));
        $this->assertFalse($this->object->validateModel(new stdClass(), "notanentity"), false);
    }

    public function testValidateModelEntityBadException()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Entity must implement or extend notanentity");
        $this->assertFalse($this->object->validateModel($this->getModel(), "notanentity", true));
    }

    public function testValidateModelEntityBadBoolean()
    {
        $this->assertFalse($this->object->validateModel($this->getModel(), "notanentity"));
        $this->assertFalse($this->object->validateModel($this->getModel(), "notanentity", false));
    }

    public function testValidateModelEntityGood()
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $this->assertTrue($this->object->validateModel($this->getModel(0, [], $entity), EntityInterface::class));
    }
}
