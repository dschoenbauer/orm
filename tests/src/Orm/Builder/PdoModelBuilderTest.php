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
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Description of PdoModelBuilderTest
 *
 * @author David Schoenbauer
 */
class PdoModelBuilderTest extends TestCase
{

    protected $object;

    protected function setUp()
    {
        $adapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $this->object = new PdoModelBuilder($adapter, $entity);
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
    
    public function testAdapter(){
        $adapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($adapter, $this->object->setAdapter($adapter)->getAdapter());
    }
    
    public function testBuildPersistence(){
        $model = $this->getMockBuilder(CrudModel::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->exactly(4))->method('accept')->willReturnSelf();
        $this->object->setModel($model)->buildPersistence();
    }
    
    public function testBuildValidations(){
        $model = $this->getMockBuilder(CrudModel::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->exactly(7))->method('accept')->willReturnSelf();
        $this->object->setModel($model)->buildValidations();
    }
    
    public function testBuildFinalOutput(){
        $model = $this->getMockBuilder(CrudModel::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->exactly(0))->method('accept')->willReturnSelf();
        $this->object->setModel($model)->buildFinalOutput();
    }
    
    
}
