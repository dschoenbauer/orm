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
namespace DSchoenbauer\Orm\Events\Validate\DataType;

use DSchoenbauer\Orm\Exception\InvalidDataTypeException;
use PHPUnit\Framework\TestCase;

/**
 * Description of AbstractValidateTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class AbstractDataTypeTest extends TestCase
{

    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(AbstractDataType::class);
    }

    public function testValidateAllFieldsChecked()
    {
        $data = ['id' => 1, 'calls' => 1, 'NoGood' => 2];
        $fields = ['id', 'calls'];
        $this->object->expects($this->exactly(2))->method('validateValue')->with(1)->willReturn(true);
        $this->object->validate($data, $fields);
    }

    public function testValidateAllFieldsCheckedMore()
    {
        $data = ['id' => 1, 'calls' => 1, 'NoGood' => 2, 'g' => 1, 'h' => 1, 'i' => 1];
        $fields = ['id', 'calls', 'g', 'h', 'i'];
        $this->object->expects($this->exactly(5))->method('validateValue')->with(1)->willReturn(true);
        $this->object->validate($data, $fields);
    }

    public function testValidateFieldsError()
    {
        $data = ['id' => 1, 'calls' => 1, 'NoGood' => 2, 'g' => 1, 'h' => 1, 'i' => 1];
        $fields = ['id', 'calls', 'g', 'h', 'i'];
        $this->object->expects($this->exactly(1))->method('validateValue')->with(1)->willReturn(false);
        $this->expectException(InvalidDataTypeException::class);
        $this->object->validate($data, $fields);
    }
}
