<?php
/**
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
namespace DSchoenbauer\Orm\Entity;

use DSchoenbauer\Tests\Orm\Entity\AbstractEntityWithAll;
use DSchoenbauer\Tests\Orm\Entity\AbstractEntityWithBool;
use DSchoenbauer\Tests\Orm\Entity\AbstractEntityWithDate;
use DSchoenbauer\Tests\Orm\Entity\AbstractEntityWithNumber;
use DSchoenbauer\Tests\Orm\Entity\AbstractEntityWithString;
use PHPUnit\Framework\TestCase;

/**
 * Description of AbstractEntityTest
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class AbstractEntityTest extends TestCase
{

    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(AbstractEntity::class);
    }

    public function testName()
    {
        $this->assertEquals("test", $this->object->setName("test")->getName());
    }

    public function testIdField()
    {
        $this->assertEquals('id', $this->object->setIdField('id')->getIdField());
    }

    public function testTable()
    {
        $this->assertEquals('some-table',
            $this->object->setTable('some-table')->getTable());
    }

    public function testGetAllFieldsEmpty()
    {
        $this->assertEquals([], $this->object->getAllFields());
    }

    public function testGetAllFieldsBool()
    {
        $entity = new AbstractEntityWithBool();
        $this->assertEquals(['boolField'], $entity->getAllFields());
    }

    public function testGetAllFieldsDate()
    {
        $entity = new AbstractEntityWithDate();
        $this->assertEquals(['dateField'], $entity->getAllFields());
    }

    public function testGetAllFieldsNumber()
    {
        $entity = new AbstractEntityWithNumber();
        $this->assertEquals(['numberField'], $entity->getAllFields());
    }

    public function testGetAllFieldsString()
    {
        $entity = new AbstractEntityWithString();
        $this->assertEquals(['stringField'], $entity->getAllFields());
    }

    public function testGetAllFieldsAll()
    {
        $entity = new AbstractEntityWithAll();
        $this->assertEquals(['boolField', 'dateField', 'numberField', 'stringField'],
            $entity->getAllFields());
    }
}
