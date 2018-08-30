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
namespace DSchoenbauer\Orm\Events\Validate\Schema;

use DSchoenbauer\Orm\Entity\HasRequiredFieldsInterface;
use DSchoenbauer\Orm\Exception\RequiredFieldMissingException;
use PHPUnit\Framework\TestCase;

/**
 * Ensures required fields are present
 *
 * @author David Schoenbauer
 */
class RequiredFieldsTest extends TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new RequiredFields();
    }

    public function testGetFields()
    {
        $fields = ['field', 'field1', 'field3'];
        $entity = $this->getMockBuilder(HasRequiredFieldsInterface::class)->getMock();
        $entity->expects($this->exactly(1))->method('getRequiredFields')->willReturn($fields);
        $this->assertEquals($fields, $this->object->getFields($entity));
    }

    public function testTypeInterface()
    {
        $this->assertEquals(HasRequiredFieldsInterface::class, $this->object->getInterface());
    }

    /**
     * @dataProvider validateDataProvider
     * @param array $data
     * @param array $fields
     * @param boolean $result
     */
    public function testValidate($data, $fields, $result)
    {
        if (!$result) {
            $this->expectException(RequiredFieldMissingException::class);
            $this->object->validate($data, $fields);
        }else{
            $this->assertTrue($this->object->validate($data, $fields));
        }
    }

    public function validateDataProvider()
    {
        return[
            "Nothing Wrong" => [['id' => 1, 'name' => 2], ['id', 'name'], true],
            "Only 1 field required" => [['id' => 1, 'name' => 2], ['name'], true],
            "Zero fields required" => [['id' => 1, 'name' => 2], [], true],

            "Missing a Required Fields" => [['id' => 1, 'name' => 2], ['ack'], false],
        ];
    }
}
