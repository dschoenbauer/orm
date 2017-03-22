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

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Model;
use PHPUnit_Framework_TestCase;

/**
 * Description of ValidFields
 *
 * @author David Schoenbauer
 */
class ValidFieldsTest extends PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new ValidFields();
    }

    public function testGetFields()
    {
        $fields = ['test1', 'test2', 'test3'];
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->once())->method('getAllFields')->willReturn($fields);
        $this->assertEquals($fields, $this->object->getFields($entity));
    }

    public function testTypeInterface()
    {
        $this->assertEquals(EntityInterface::class, $this->object->getTypeInterface());
    }

    /**
     * @dataProvider valdateDataProvider
     * @param type $fields
     * @param type $data
     * @param type $result
     */
    public function testValidate($fields, $data, $result)
    {
        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->once())->method('setData')->willReturnCallback(function($data) {
            $this->_data = $data;
            return $this;
        });
        $model->expects($this->once())->method('getData')->willReturnCallback(function() {
            return $this->_data;
        });
        $this->assertTrue($this->object->setModel($model)->validate($data, $fields));
        $this->assertEquals($result, $this->object->getModel()->getData());
    }

    public function valdateDataProvider()
    {
        return [
            'exact match' => [
                ['id', 'name', 'timestamp'],
                ['id' => 1, 'name' => 'test', 'timestamp' => '2016-01-01 05:00:00'],
                ['id' => 1, 'name' => 'test', 'timestamp' => '2016-01-01 05:00:00']
            ],
            'misspelled fields' => [
                ['id', 'name', 'timestamp'],
                ['id' => 1, 'name' => 'test', 'timestampe' => '2016-01-01 05:00:00'],
                ['id' => 1, 'name' => 'test',]
            ],
            'no matching fields' => [
                ['idx', 'first-name', 'time-stamp'],
                ['id' => 1, 'name' => 'test', 'timestamp' => '2016-01-01 05:00:00'],
                []
            ],
            'captialization differences' => [
                ['ID', 'name', 'timeStamp'],
                ['id' => 1, 'name' => 'test', 'timestamp' => '2016-01-01 05:00:00'],
                ['name' => 'test',]
            ],
        ];
    }
}
