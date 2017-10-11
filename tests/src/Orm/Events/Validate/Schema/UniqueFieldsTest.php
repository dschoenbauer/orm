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
namespace DSchoenbauer\Orm\Events\Validate\Schema;

use DSchoenbauer\Orm\Entity\HasUniqueFieldsInterface;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of UniqueFieldsTest
 *
 * @author David Schoenbauer
 */
class UniqueFieldsTest extends TestCase
{

    private $adapter;
    private $object;

    use TestModelTrait;

    protected function setUp()
    {
        $this->adapter = $this->getMockBuilder(PDO::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->object = new UniqueFields();
    }

    public function testParent()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->object);
    }

    public function testAdapter()
    {
        $this->assertSame($this->adapter, $this->object->setAdapter($this->adapter)->getAdapter());
    }

    public function testExecuteBadModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testExecuteGoodModelBadInterface()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($this->getModel());
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testExecuteModelAndInterface()
    {
        $fields = ['test'];
        $data = ['id' => 1, 'test' => 'bob'];
        $this->object = $this->getMockBuilder(UniqueFields::class)->setMethods(['checkField'])->getMock();
        //$this->object->expects($this->once())->method('checkField')->with($fields, $data);

        $entity = $this->getMockBuilder(HasUniqueFieldsInterface::class)->getMock();
        $entity->expects($this->once())->method('getUniqueFields')->willReturn($fields);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($this->getModel(0, [], $entity));
        $this->assertTrue($this->object->onExecute($event));
    }
}
