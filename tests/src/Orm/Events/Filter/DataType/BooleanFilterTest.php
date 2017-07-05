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

use DSchoenbauer\Orm\Entity\HasBoolFieldsInterface;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;

/**
 * Description of BooleanTest
 *
 * @author David Schoenbauer
 */
class BooleanFilterTest extends TestCase
{

    use TestModelTrait;

    protected $object;

    protected function setUp()
    {
        $this->object = new BooleanFilter();
    }

    public function testGetFields()
    {
        $fields = ['hasMom', 'hasDad', 'hasSiblings'];
        $entity = $this->getMockBuilder(HasBoolFieldsInterface::class)->getMock();
        $entity->expects($this->once())->method('getBoolFields')->willReturn($fields);
        $model = $this->getModel(0, [], $entity);

        $this->assertEquals($fields, $this->object->getFields($model));
    }

    public function testGetFieldsFail()
    {
        $model = $this->getModel(0, [], $this->getAbstractEntity());
        $this->assertEquals([], $this->object->getFields($model));
    }

    /**
     * @dataProvider dataProviderFormat
     */
    public function testFormat($data, $fields, $result)
    {
        $this->assertEquals($result, $this->object->formatValue($data, $fields));
    }

    public function dataProviderFormat()
    {
        return [
            'Passive Text' => [['id' => 1, 'bool' => 'sure', 'number' => 1], ['bool'], ['id' => 1, 'bool' => true, 'number' => 1]],
            'True Text' => [['id' => 1, 'bool' => 'true', 'number' => 1], ['bool'], ['id' => 1, 'bool' => true, 'number' => 1]],
            'False Text' => [['id' => 1, 'bool' => 'false', 'number' => 1], ['bool'], ['id' => 1, 'bool' => true, 'number' => 1]],
            'Null' => [['id' => 1, 'bool' => null, 'number' => 1], ['bool'], ['id' => 1, 'bool' => false, 'number' => 1]],
            'Numeric False' => [['id' => 1, 'bool' => 0, 'number' => 1], ['bool'], ['id' => 1, 'bool' => false, 'number' => 1]],
            'Numeric True' => [['id' => 1, 'bool' => 1234, 'number' => 1], ['bool'], ['id' => 1, 'bool' => true, 'number' => 1]],
        ];
    }

    /**
     * @dataProvider dataProviderFormat
     */
    public function testFilter($data, $fields, $result)
    {
        $entity = $this->getMockBuilder(HasBoolFieldsInterface::class)->getMock();
        $entity->expects($this->once())->method('getBoolFields')->willReturn($fields);
        $model = $this->getModel(0, $data, $entity);
        $this->assertEquals($result, $this->object->setModel($model)->filter($data));
    }

    public function testTrueResult()
    {
        $this->assertEquals('1', $this->object->setTrueResult('1')->getTrueResult());
    }

    public function testTrueResultDefault()
    {
        $this->assertTrue($this->object->getTrueResult());
    }

    public function testFalseResult()
    {
        $this->assertEquals('0', $this->object->setFalseResult('0')->getFalseResult());
    }

    public function testFalseResultDefault()
    {
        $this->assertFalse($this->object->getFalseResult());
    }
}
