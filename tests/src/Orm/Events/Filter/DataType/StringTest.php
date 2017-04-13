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
namespace DSchoenbauer\Orm\Events\Filter\DataType;

use DSchoenbauer\Orm\Entity\HasStringFieldsInterface;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;

/**
 * Description of StringTest
 *
 * @author David Schoenbauer
 */
class StringTest extends TestCase
{

    use TestModelTrait;

    protected $object;

    protected function setUp()
    {
        $mockEntity = $this->getMockBuilder(HasStringFieldsInterface::class)->getMock();
        $mockEntity->expects($this->any())->method('getStringFields')->willReturn(['test1', 'test2', 'test3']);
        $model = $this->getModel(0, [], $mockEntity);

        $this->object = new String();
        $this->object->setModel($model);
    }

    public function testFilter()
    {
        $data = ['test1' => 1, 'test2' => false, 'test3' => 'this is a test'];
        $expected = ['test1' => '1', 'test2' => '', 'test3' => 'this is a test'];
        $this->assertEquals($expected, $this->object->filter($data));
    }

    public function testFilterNotText()
    {
        $data = ['test1' => new \stdClass(), 'test2' => new \DateTime()];
        $this->assertEquals($data, $this->object->filter($data));
    }

    public function testFilterBadEntity()
    {
        $model = $this->getModel(0, [], null);

        $data = ['test1' => 1, 'test2' => false, 'test3' => 'this is a test'];
        $expected = ['test1' => '1', 'test2' => '', 'test3' => 'this is a test'];
        $this->assertEquals($expected, $this->object->setModel($model)->filter($data));
    }
}
