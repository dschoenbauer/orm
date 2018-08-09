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
namespace DSchoenbauer\Orm\DataProvider;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Sql\Command\Select;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Description of EntityDataProviderTest
 *
 * @author David Schoenbauer
 */
class EntityDataProviderTest extends TestCase
{
    /* @var $variable EntityDataProvider */

    private $object;

    protected function setUp()
    {
        $this->object = new EntityDataProvider();
    }

    public function testHasInterface()
    {
        $this->assertInstanceOf(DataProviderInterface::class, $this->object);
    }

    public function testAdapter()
    {
        $adapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($adapter, $this->object->setAdapter($adapter)->getAdapter());
    }

    public function testEntity()
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $this->assertSame($entity, $this->object->setEntity($entity)->getEntity());
    }

    public function testSelect()
    {
        $select = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $this->assertInstanceOf(Select::class, $this->object->getSelect(), 'Valdiate Lazy Load');
        $this->assertSame($select, $this->object->setSelect($select)->getSelect());
    }

    public function testGetData()
    {
        $result = [1 => ['id' => 1, 'test' => '1']];
        $adapter = $this->getMockBuilder(\PDO::class)->disableOriginalConstructor()->getMock();
        $table = "test";

        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->atLeast(1))->method('getTable')->willReturn($table);
        $entity->expects($this->atLeast(1))->method('getIdField')->willReturn('id');
        $entity->expects($this->atLeast(1))->method('getAllFields')->willReturn(['id', 'test']);

        $select = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $select->expects($this->atLeast(1))->method('setTable')->with($table)->willReturnSelf();
        $select->expects($this->atLeast(1))->method('setFields')->with(['id idx', 'id', 'test'])->willReturnSelf();
        $select->expects($this->atLeast(1))->method('setFetchStyle')->with(\PDO::FETCH_ASSOC | \PDO::FETCH_UNIQUE)->willReturnSelf();
        $select->expects($this->atLeast(1))->method('setFetchFlat')->with(false)->willReturnSelf();
        $select->expects($this->atLeast(1))->method('execute')->with($adapter)->willReturn($result);

        $this->assertEquals($result, $this->object->setAdapter($adapter)->setSelect($select)->setEntity($entity)->getData());
    }
}
