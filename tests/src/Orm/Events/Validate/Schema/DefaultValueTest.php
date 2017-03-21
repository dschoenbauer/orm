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

use DSchoenbauer\Orm\Entity\HasDefaultValuesInterface;
use DSchoenbauer\Orm\Enum\ModelEvents;
use DSchoenbauer\Orm\Model;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\Event;

/**
 * Description of DefaultValues
 *
 * @author David Schoenbauer
 */
class DefaultValueTest extends PHPUnit_Framework_TestCase
{

    protected $object;

    protected function setUp()
    {
        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->any())->method('setData')->willReturnCallback(function($value) {
            $this->data = $value;
            return $this;
        });
        $model->expects($this->any())->method('getData')->willReturnCallback(function() {
            return $this->data;
        });

        $this->object = new DefaultValue();
        $this->object->setModel($model);
    }

    public function testGetFields()
    {
        $fields = ['id' => 1, 'name' => 'test'];

        $entity = $this->getMockBuilder(HasDefaultValuesInterface::class)->getMock();
        $entity->expects($this->exactly(1))->method('getDefaultValues')->willReturn($fields);

        $this->assertEquals($fields, $this->object->getFields($entity));
    }

    public function testGetTypeInterface()
    {
        $this->assertEquals(HasDefaultValuesInterface::class, $this->object->getTypeInterface());
    }

    public function testValidateGoldenPath()
    {
        $this->object->setParams(['events' => [ModelEvents::CREATE]]);
        $data = ['id' => 1, 'name' => 'ted'];
        $fields = ['id' => 999, 'name' => 'rupert', 'ack' => true];
        $default = ['id' => 1, 'name' => 'ted', 'ack' => true];
        $this->assertTrue($this->object->validate($data, $fields));
        $this->assertEquals($default, $this->object->getModel()->getData());
    }

    public function testValidateAllValuesProvidedNoDefault()
    {
        $this->object->setParams(['events' => [ModelEvents::CREATE]]);
        $data = ['id' => 1, 'name' => 'ted'];
        $fields = ['id' => 999, 'name' => 'rupert'];
        $this->assertTrue($this->object->validate($data, $fields));
        $this->assertEquals($data, $this->object->getModel()->getData());
    }

    public function testValidatePoorFormat()
    {
        $this->object->setParams(['events' => ModelEvents::CREATE]);
        $data = ['id' => 1, 'name' => 'ted'];
        $fields = ['id' => 999, 'name' => 'rupert', 'ack' => true];
        $this->assertTrue($this->object->validate($data, $fields));
    }
}
