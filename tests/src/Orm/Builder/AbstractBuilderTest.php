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
namespace DSchoenbauer\Orm\Builder;

use DSchoenbauer\Orm\CrudModel;
use DSchoenbauer\Orm\Entity\EntityInterface;
use PHPUnit\Framework\TestCase;

/**
 * Description of AbstractBuilderTest
 *
 * @author David Schoenbauer
 */
class AbstractBuilderTest extends TestCase
{
    protected $object;
    
    protected function setUp()
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $this->setObject($this->getMockForAbstractClass(AbstractBuilder::class,[$entity]));
    }
    
    /**
     * @return AbstractBuilder
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
    
    
    
    public function testHasProperInterface(){
        $this->assertInstanceOf(BuilderInterface::class, $this->getObject());
    }
    
    public function testCrudModel(){
        $model = $this->getMockBuilder(CrudModel::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($model, $this->object->setModel($model)->getModel());
        $this->assertSame($model, $this->object->setModel($model)->build());
    }
    
    public function testCrudBuild(){
        $model = $this->getMockBuilder(CrudModel::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($model, $this->object->setModel($model)->build());
    }
    
    public function testBuildValidations(){
        $model = $this->getMockBuilder(CrudModel::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->exactly(1))->method('accept')->willReturnSelf();
        $this->object->setModel($model)->addValidations();
    }
    
    public function testBuildFinalOutput(){
        $model = $this->getMockBuilder(CrudModel::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->exactly(1))->method('accept')->willReturnSelf();
        $this->object->setModel($model)->addFinalOutput();
    }
}
