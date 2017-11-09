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
namespace DSchoenbauer\Orm\Exception;

use DSchoenbauer\Exception\Http\ClientError\BadRequestException;
use DSchoenbauer\Orm\Enum\ExceptionDefaultMessages;
use PHPUnit\Framework\TestCase;

/**
 * Thrown when a data type is provided other than the required data type
 *
 * @author David Schoenbauer
 */
class RequiredFieldMissingExceptionTest extends TestCase
{

    private $object;

    protected function setUp()
    {
        $this->object = new RequiredFieldMissingException();
    }

    public function testHasCoreInterface()
    {
        $this->assertInstanceOf(OrmExceptionInterface::class, $this->object);
    }

    public function testHasCorrectBaseException()
    {
        $this->assertInstanceOf(BadRequestException::class, $this->object);
    }

    public function testMissingFields()
    {
        $this->assertEquals([], $this->object->getMissingFields());
        $this->assertEquals(['test'], $this->object->setMissingFields(['test'])->getMissingFields());
    }

    public function testCustomMessage()
    {
        $message = "My Message";
        $this->object = new RequiredFieldMissingException([], $message);
        $this->assertEquals($message, $this->object->getMessage());
    }

    public function testNoMessageFields()
    {
        $expected = sprintf(ExceptionDefaultMessages::REQUIRED_FIELD_MISSING_EXCEPTION, 'name, desc');
        $this->object = new RequiredFieldMissingException(['name', 'desc']);
        $this->assertEquals($expected, $this->object->getMessage());
    }

    public function testNoMessageNoFields()
    {
        $expected = sprintf(ExceptionDefaultMessages::REQUIRED_FIELD_MISSING_EXCEPTION, 'no fields identified');
        $this->object = new RequiredFieldMissingException();
        $this->assertEquals($expected, $this->object->getMessage());
    }

    /**
     * 
     * @param type $expected
     * @param type $message
     * @param array $fields
     * @dataProvider getDataProvider
     */
    public function testInterpolateMessage($expected, $message, array $fields)
    {
        $this->assertEquals($expected, $this->object->interpolateMessage($message, $fields));
    }

    public function getDataProvider()
    {
        return [
            ['this is a test: id', 'this is a test: %s', ['id']],
            ['this is a test: id, name', 'this is a test: %s', ['id', 'name']]
        ];
    }

    public function testCustomMessageWithFields()
    {

        $this->object = new RequiredFieldMissingException(['id'], 'Field missing: %s');
        $expected = "Field missing: id";
        $this->assertEquals($expected, $this->object->getMessage());
    }
}
