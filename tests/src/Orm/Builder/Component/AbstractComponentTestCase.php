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
namespace DSchoenbauer\Tests\Orm\Builder\Component;

use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Description of AbstractComponentTestCase
 *
 * @author David Schoenbauer
 */
class AbstractComponentTestCase extends TestCase
{
    private $calls = 0;
    protected $object;


    public function testVisitModel(){
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->exactly($this->getCalls()))->method('accept')->willReturnSelf();
        $this->object->visitModel($model);
    }

    public function testInterface(){
        $this->assertInstanceOf(VisitorInterface::class, $this->object);
    }


    public function getCalls()
    {
        return $this->calls;
    }

    public function setCalls($calls)
    {
        $this->calls = $calls;
        return $this;
    }
}
