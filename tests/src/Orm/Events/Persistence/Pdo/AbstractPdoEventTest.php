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
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\ModelInterface;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of AbstractPdoEventTest
 *
 * @author David Schoenbauer
 */
class AbstractPdoEventTest extends TestCase
{

    /**
     *
     * @var AbstractPdoEvent
     */
    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(AbstractPdoEvent::class, [], '', false);
    }

    public function testAdapter()
    {
        $adapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($adapter, $this->object->setAdapter($adapter)->getAdapter());
    }

    public function testConstruct()
    {

        $events = ['test', 'test2'];
        $adapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $priority = "HIGH";
        $this->object = $this->getMockForAbstractClass(AbstractPdoEvent::class, [$events, $adapter, $priority], '', true);
        $this->assertSame($adapter, $this->object->getAdapter());
        $this->assertEquals($events, $this->object->getEvents());
        $this->assertEquals($priority, $this->object->getPriority());
    }

    public function testOnExecuteBadTarget()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteGoodTargetBadInterface()
    {
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteGood()
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getEntity')->willReturn($entity);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->object->expects($this->once())->method('commit')->with($event);
        $this->object->onExecute($event);
    }

    /**
     * 
     * @param array $result
     * @param array $data
     * @param array $fields
     * @dataProvider reduceFieldsDataProvider
     */
    public function testReduceFields(array $result, array $data, array $fields)
    {
        $this->assertEquals($result, $this->object->reduceFields($data, $fields));
    }

    public function reduceFieldsDataProvider()
    {
        $dateObj = new DateTimeExt();
        return [
            'Too Many Fields' => [['id' => 1, 'name' => 'bob'], ['id' => 1, 'name' => 'bob', 'xtra' => true], ['id', 'name']],
            'Under Fields' => [['id' => 1, 'name' => 'bob'], ['id' => 1, 'name' => 'bob'], ['id', 'name', 'description', 'active']],
            'Just Right' => [['id' => 1, 'name' => 'bob'], ['id' => 1, 'name' => 'bob'], ['id', 'name']],
            'Boolean' => [['id' => 1, 'name' => 'bob', 'active' => true], ['id' => 1, 'name' => 'bob', 'active' => true], ['id', 'name', 'active']],
            'scalar only' => [['id' => 1], ['id' => 1, 'array' => [1, 2], 'object' => new \stdClass()], ['id', 'array', 'object']],
            'objects with _toString' => [['id' => 1, 'date' => $dateObj], ['id' => 1, 'array' => [1, 2], 'date' => $dateObj], ['id', 'array', 'date']],
        ];
    }
}

/**
 * Naughty I know.
 */
class DateTimeExt extends \DateTime
{

    public function __toString()
    {
        return $this->format("Y-m-d H:i:s");
    }
}
