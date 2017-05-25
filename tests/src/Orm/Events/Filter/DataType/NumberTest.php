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

use DSchoenbauer\Orm\Entity\HasNumericFieldsInterface;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;

/**
 * Description of NumberTest
 *
 * @author David Schoenbauer
 */
class NumberTest extends TestCase
{

    use TestModelTrait;

    protected $object;

    protected function setUp()
    {
        $this->object = new Number();
    }

    public function testGetFields()
    {
        $fields = ['countOfMom', 'countOfDad', 'countOfSiblings'];
        $entity = $this->getMockBuilder(HasNumericFieldsInterface::class)->getMock();
        $entity->expects($this->once())->method('getNumericFields')->willReturn($fields);
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
            'Passive Text' => [['id' => 1, 'xtraNumeric' => 'sure', 'number' => 1], ['id', 'number', 'xtraNumeric'], ['id' => 1, 'xtraNumeric' => 0, 'number' => 1]],
            'Float' => [['id' => 1, 'xtraNumeric' => '1.01', 'number' => 1.01], ['id', 'number', 'xtraNumeric'], ['id' => 1, 'xtraNumeric' => 1.01, 'number' => 1.01]],
            'Int' => [['id' => 1, 'xtraNumeric' => '1', 'number' => 1], ['id', 'number', 'xtraNumeric'], ['id' => 1, 'xtraNumeric' => 1, 'number' => 1]],
            'Small' => [['id' => 1, 'xtraNumeric' => '0.0001', 'number' => 0.0001], ['id', 'number', 'xtraNumeric'], ['id' => 1, 'xtraNumeric' => 0.0001, 'number' => 0.0001]],
            'Large' => [['id' => 1, 'xtraNumeric' => '10000', 'number' => 10000], ['id', 'number', 'xtraNumeric'], ['id' => 1, 'xtraNumeric' => 10000, 'number' => 10000]],
        ];
    }

    /**
     * @dataProvider dataProviderFormat
     */
    public function testFilter($data, $fields, $result)
    {
        $entity = $this->getMockBuilder(HasNumericFieldsInterface::class)->getMock();
        $entity->expects($this->once())->method('getNumericFields')->willReturn($fields);
        $model = $this->getModel(0, $data, $entity);
        $this->assertEquals($result, $this->object->setModel($model)->filter($data));
    }
}
