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

use DSchoenbauer\Orm\Model;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface ;

/**
 * Description of RemoveIdTest
 *
 * @author David Schoenbauer
 */
class RemoveIdTest extends TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new RemoveId();
    }

    public function testOnExecuteNoModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }

    public function dataProviderOnExecuteClearsId()
    {
        return [
            'Clears Id' => [['id' => 1, 'test' => true], ['test' => true], 'id'],
            'Id not present' => [['id' => 1, 'test' => true], ['id' => 1, 'test' => true], 'idx'],
            'id located in random spot' => [['test' => true, 'id' => 1,], ['test' => true], 'id'],
            'empty data' => [[], [], 'id'],
        ];
    }

    /**
     * @dataProvider dataProviderOnExecuteClearsId
     * @param type $data
     * @param array $result
     * @param string $idField
     */
    public function testOnExecuteClearsId($data, $result, $idField)
    {

        $entity = $this->getMockBuilder(\DSchoenbauer\Orm\Entity\EntityInterface::class)->getMock();
        $entity->expects($this->once())->method('getIdField')->willReturn($idField);

        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->exactly(1))->method('getEntity')->willReturn($entity);
        $model->expects($this->exactly(2))->method('setData')->willReturnCallback(function($data) {
            $this->data = $data;
            return $this;
        });
        $model->expects($this->exactly(2))->method('getData')->willReturnCallback(function() {
            return $this->data;
        });
        $model->setData($data);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(1))->method('getTarget')->willReturn($model);

        $this->assertTrue($this->object->onExecute($event));
        $this->assertEquals($result, $model->getData());
    }
}
