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

use DateTime;
use DSchoenbauer\Orm\Entity\HasDateFieldsInterface;
use DSchoenbauer\Orm\Entity\HasDateWithCustomFormatInterface;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Description of ValidateDate
 *
 * @author David Schoenbauer
 */
class ValidateDateTest extends PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new ValidateDate();
    }

    public function testGetFields()
    {
        $data = ['id', 'calls', 'days', 'hours'];
        $entity = $this->getMockBuilder(HasDateFieldsInterface::class)->getMock();
        $entity->expects($this->once())->method('getDateFields')->willReturn($data);
        $this->assertEquals($data, $this->object->getFields($entity));
    }

    public function testGetTypeInterface()
    {
        $this->assertEquals(
            HasDateFieldsInterface::class, $this->object->getTypeInterface()
        );
    }

    public function testValidateValue()
    {
        $i = 0;
        $this->assertFalse($this->object->validateValue(1), $i++);
        $this->assertFalse($this->object->validateValue(1.0), $i++);
        $this->assertFalse($this->object->validateValue(0.001), $i++);
        $this->assertFalse($this->object->validateValue('a'), $i++);
        $this->assertTrue($this->object->validateValue(null), $i++);
        $this->assertTrue($this->object->validateValue(new DateTime()), $i++);
        $this->assertFalse($this->object->validateValue(new stdClass()), $i++);
    }

    public function testDefaultDateTimeFormat()
    {
        $this->assertEquals(\DateTime::ISO8601,
            $this->object->setDefaultDateTimeFormat(\DateTime::ISO8601)->getDefaultDateTimeFormat());
    }

    public function testValidateBadDate()
    {
        //ISO8601 = "Y-m-d\TH:i:sO";
        $this->assertFalse($this->object->validateValue('2013-13-01'));
    }

    public function testValidateGoodDate()
    {
        //ISO8601 = "Y-m-d\TH:i:sO";
        $this->assertTrue(
            $this->object
                ->setDefaultDateTimeFormat(\DateTime::ISO8601)
                ->validateValue('2017-03-15T18:42:12+00:00')
        );
    }

    public function testCustomFormats()
    {
        $customFormats = [
            'usDate' => 'm/d/Y',
            'euDate' => 'Y/m/d'
        ];
        $fields = array_keys($customFormats);
        $fields[] = "default";
        $entity = $this->getMockBuilder(HasDateWithCustomFormatInterface::class)->getMock();
        $entity->expects($this->exactly(1))->method('getDateCustomFormat')->willReturn($customFormats);
        $entity->expects($this->exactly(1))->method('getDateDefaultFormat')->willReturn(\DateTime::ISO8601);
        $entity->expects($this->exactly(1))->method('getDateFields')->willReturn($fields);

        $this->object->getFields($entity);

        //Default
        $this->assertTrue($this->object->validateValue('2017-03-15T18:42:12+05:00',
                'default'));
        $this->assertFalse($this->object->validateValue('2013/13/01 13:00:00',
                'default'));
        //Us Date
        $this->assertTrue($this->object->validateValue('01/13/2013', 'usDate'));
        $this->assertTrue($this->object->validateValue('01/13/13', 'usDate'));
        $this->assertFalse($this->object->validateValue('2013/13/01', 'usDate'));
        //EU Date
        $this->assertTrue($this->object->validateValue('2013/13/01', 'euDate'));
        $this->assertFalse($this->object->validateValue('01/13/2013', 'euDate'));
    }
}
