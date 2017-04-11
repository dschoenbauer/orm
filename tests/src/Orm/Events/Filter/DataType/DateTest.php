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

use DateTime;
use DSchoenbauer\Orm\Entity\HasDateFieldsInterface;
use DSchoenbauer\Orm\Entity\HasDateWithCustomFormatInterface;
use DSchoenbauer\Orm\Exception\InvalidDataTypeException;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;

/**
 * Description of Date
 *
 * @author David Schoenbauer
 */
class DateTest extends TestCase
{

    use TestModelTrait;

    protected $object;

    protected function setUp()
    {
        $this->object = new Date();
    }

    public function testGetDateFormatsBadEntity()
    {
        $model = $this->getModel();
        $this->assertEquals([], $this->object->getDateFormats($model));
    }

    public function testGetDateFormatsOnlyHasDates()
    {
        $entity = $this->getMockBuilder(HasDateFieldsInterface::class)->getMock();
        $entity->expects($this->once())->method('getDateFields')->willReturn(['date', 'timeStamp']);
        $entity->expects($this->once())->method('getDateDefaultFormat')->willReturn('bob');
        $model = $this->getModel(0, [], $entity);

        $this->assertEquals(['date' => 'bob', 'timeStamp' => 'bob'], $this->object->getDateFormats($model));
    }

    public function testGetDateFormatsOnlyHasCustomDates()
    {
        $entity = $this->getMockBuilder(HasDateWithCustomFormatInterface::class)->getMock();
        $entity->expects($this->once())->method('getDateFields')->willReturn(['date', 'timeStamp']);
        $entity->expects($this->once())->method('getDateDefaultFormat')->willReturn('bob');
        $entity->expects($this->once())->method('getDateCustomFormat')->willReturn(['timeStamp' => 'jan']);
        $model = $this->getModel(0, [], $entity);

        $this->assertEquals(['date' => 'bob', 'timeStamp' => 'jan'], $this->object->getDateFormats($model));
    }

    public function testFilterEmptyData()
    {
        $this->object->setModel($this->getModel());
        $this->assertEquals([], $this->object->filter([]));
    }
    
    public function testFilterDateFieldNotExist(){
        $data = ['notPresent' => 'test'];
        $this->object->setModel($this->getFullModel(['date'], DATE_ISO8601, []));
        $this->assertEquals($data, $this->object->filter($data));
    }
    
    public function testFilterDateFieldDateObject(){
        $data = ['dateObject' => new DateTime()];
        $this->object->setModel($this->getFullModel(['dateObject'], DATE_ISO8601, []));
        $this->assertEquals($data, $this->object->filter($data));        
    }
    
    public function testFilterBadDateFormat(){
        $data = ['date' => 'bogusValue'];
        $this->expectException(InvalidDataTypeException::class);
        $this->object->setModel($this->getFullModel(['date'], DATE_ISO8601, []));
        $this->assertEquals($data, $this->object->filter($data));        
    }
    
    public function testFilter(){
        $data = ['dateObject' => '2010-12-30T23:21:46+1100'];
        $dataResult = ['dateObject' => new DateTime('2010-12-30T23:21:46+1100')];
        $this->object->setModel($this->getFullModel(['dateObject'], DATE_ISO8601, []));
        $this->assertEquals($dataResult, $this->object->filter($data));        
    }
    
    public function getFullModel($dateFields = [], $defaultValue = '', $getDateCustomFormats = []){
        $entity = $this->getMockBuilder(HasDateWithCustomFormatInterface::class)->getMock();
        $entity->expects($this->once())->method('getDateFields')->willReturn($dateFields);
        $entity->expects($this->once())->method('getDateDefaultFormat')->willReturn($defaultValue);
        $entity->expects($this->once())->method('getDateCustomFormat')->willReturn($getDateCustomFormats);
        return $this->getModel(0, [], $entity);

    }
    
}
