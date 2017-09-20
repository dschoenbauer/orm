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

use DSchoenbauer\Orm\Entity\HasFieldsToRemoveInterface;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of RemoveFieldsTest
 *
 * @author David Schoenbauer
 */
class RemoveFieldsTest extends TestCase
{

    use TestModelTrait;

    private $object;

    protected function setUp()
    {
        $this->object = new RemoveFields();
    }

    public function testHasProperParent()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->object);
    }

    public function testOnExecuteNotModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn(null);
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteNotInterface()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($this->getModel());
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecute()
    {
        $fields = ['id', 'password'];
        $data = ['id' => 1, 'password' => 'somethingSecretD', 'name' => 'John Doe'];
        $result = ['name' => 'John Doe'];

        $entity = $this->getMockBuilder(HasFieldsToRemoveInterface::class)->getMock();
        $entity->expects($this->any())->method('getFieldsToRemove')->willReturn($fields);

        $model = $this->getModel(1, $data, $entity);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->assertTrue($this->object->onExecute($event));
        $this->assertEquals($result, $model->getData());
    }

    /**
     * @dataProvider revmoveFieldsDataProvider
     */
    public function testRemoveFields($data, $fields, $result)
    {
        $this->assertEquals($result, $this->object->removeFields($fields, $data));
    }

    public function revmoveFieldsDataProvider()
    {
        return [
            'simple' => [['id' => 1, 'name' => 'bob', 'order' => 1], ['id'], ['name' => 'bob', 'order' => 1]],
            'all' => [['id' => 1, 'name' => 'bob', 'order' => 1], ['id', 'name', 'order'], []],
            'none' => [['id' => 1, 'name' => 'bob', 'order' => 1], [], ['id' => 1, 'name' => 'bob', 'order' => 1]],
        ];
    }
}
