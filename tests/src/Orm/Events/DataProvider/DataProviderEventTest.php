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
namespace DSchoenbauer\Orm\Events\DataProvider;

use DSchoenbauer\Orm\DataProvider\DataProviderInterface;
use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Model;
use DSchoenbauer\Orm\ModelInterface;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * @author David Schoenbauer
 */
class DataProviderEventTest extends TestCase
{
    /* @var $object DataProviderEvent */

    private $object;
    private $dataProviderMock;

    protected function setUp()
    {
        $this->dataProviderMock = $this->getMockBuilder(DataProviderInterface::class)->disableOriginalConstructor()->getMock();
        $this->object = new DataProviderEvent([], $this->dataProviderMock);
    }

    public function testOnExecuteModelFail()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }
    
    public function testOnExecuteEntityFail()
    {
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->once())->method('getTarget')->willReturn($model);
        
        $this->assertFalse($this->object->onExecute($event));
    }
    
    public function testOnExecuteSuccess()
    {
        $data = ['some' => 'test'];
        $this->dataProviderMock->expects($this->once())->method('getData')->willReturn($data);
            
        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->once())->method('getEntity')->willReturn($this->getMockBuilder(EntityInterface::class)->getMock());
        $model->expects($this->once())->method('setData')->with($data);
        
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->once())->method('getTarget')->willReturn($model);
        
        $this->assertTrue($this->object->onExecute($event));
    }

    public function testDataProvider()
    {
        $dataProvider = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $this->assertSame($dataProvider, $this->object->setDataProvider($dataProvider)->getDataProvider());
    }
}
