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

use DSchoenbauer\Orm\Entity\HasBoolFieldsInterface;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Description of ValidateBoolean
 *
 * @author David Schoenbauer
 */
class ValidateStringTest extends PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new ValidateBoolean();
    }

    public function testGetFields()
    {
        $data = ['id', 'calls', 'days', 'hours'];
        $entity = $this->getMockBuilder(HasBoolFieldsInterface::class)->getMock();
        $entity->expects($this->once())->method('getBoolFields')->willReturn($data);
        $this->assertEquals($data, $this->object->getFields($entity));
    }

    public function testGetTypeInterface()
    {
        $this->assertEquals(
        HasBoolFieldsInterface::class, $this->object->getTypeInterface()
        );
    }

    public function testValidateValue()
    {
        $this->assertFalse($this->object->validateValue(1));
        $this->assertFalse($this->object->validateValue(1.0));
        $this->assertFalse($this->object->validateValue(0.001));
        $this->assertFalse($this->object->validateValue('a'));
        $this->assertFalse($this->object->validateValue(null));
        $this->assertFalse($this->object->validateValue(new stdClass()));
        $this->assertTrue($this->object->validateValue(false));
        $this->assertTrue($this->object->validateValue(true));
    }
}
