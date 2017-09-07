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

use DSchoenbauer\Orm\Entity\MassMappingInterface;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of MassMapperTest
 *
 * @author David Schoenbauer
 */
class MassMapperTest extends TestCase
{

    protected $object;

    use TestModelTrait;

    protected function setUp()
    {
        $this->object = new MassMapper();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->object);
    }

    public function testMappingDirection()
    {
        $this->assertEquals(MassMapper::MAPPING_IN, $this->object->setMappingDirection(MassMapper::MAPPING_IN)->getMappingDirection());
    }

    public function testMappingDirectionConstructor()
    {
        $this->object = new MassMapper([], MassMapper::MAPPING_IN);
        $this->assertEquals(MassMapper::MAPPING_IN, $this->object->getMappingDirection());
    }

    public function testOnExecuteModelNotPresent()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteModelPresentEntityIncorrect()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(1))->method('getTarget')->willReturn($this->getModel());
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteModelPresentEntityCorrect()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $entity = $this->getMockBuilder(MassMappingInterface::class)->getMock();
        $entity->expects($this->exactly(1))->method('getMapping')->willReturn([]);
        $model = $this->getModel(0,[],$entity);
        $model->expects($this->exactly(1))->method('getData')->willReturn([]);
        $model->expects($this->exactly(1))->method('setData')->with([]);
        $event->expects($this->exactly(1))->method('getTarget')->willReturn($model);
        $this->assertTrue($this->object->onExecute($event));
    }

    public function testMapData()
    {
        $mapping = [
            'fullName' => 'person.fullName',
            'lastName' => 'person.lastName',
            'firstName' => 'person.firstName',
        ];

        $data = [
            'fullName' => 'John Doe',
            'lastName' => 'Doe',
            'firstName' => 'John',
        ];
        $result = [
            'person' => [
                'fullName' => 'John Doe',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ]
        ];
        $this->assertEquals($result, $this->object->mapData($mapping, $data));
    }
    public function testMapDataReversed()
    {
        $data = [
            'person' => [
                'fullName' => 'John Doe',
                'firstName' => 'John',
                'lastName' => 'Doe',
            ]
        ];
        $result = [
            'fullName' => 'John Doe',
            'lastName' => 'Doe',
            'firstName' => 'John',
        ];
        $mapping = [
            'fullName' => 'person.fullName',
            'lastName' => 'person.lastName',
            'firstName' => 'person.firstName',
        ];

        $this->assertEquals($result, $this->object->setMappingDirection(MassMapper::MAPPING_OUT)->mapData($mapping, $data));
    }
}
