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

use DateTime;
use DSchoenbauer\Orm\Entity\HasVersionControlIndexInterface;
use DSchoenbauer\Orm\Exception\InvalidDataTypeException;
use DSchoenbauer\Orm\Exception\RecordOutOfDateException;
use DSchoenbauer\Orm\Exception\RequiredFieldMissingException;
use DSchoenbauer\Sql\Command\Select;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of VersionControlIndexTest
 *
 * @author David Schoenbauer
 */
class VersionControlIndexTest extends TestCase
{

    use TestModelTrait;
    /* @var $object VersionControlIndex */

    private $object;
    private $adapter;

    protected function setUp()
    {
        $this->adapter = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = new VersionControlIndex($this->adapter);
    }

    public function testOnExecuteNotModelTarget()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteWrongEntity()
    {
        $model = $this->getModel();
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteFieldMissing()
    {
        $entity = $this->getMockBuilder(HasVersionControlIndexInterface::class)->getMock();
        $entity->expects($this->once())->method('getVersionControlField')->willReturn('version-number');
        $model = $this->getModel(1, [], $entity);
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->expectException(RequiredFieldMissingException::class);
        $this->object->onExecute($event);
    }

    public function testOnExecuteInvalidFieldType()
    {
        $entity = $this->getMockBuilder(HasVersionControlIndexInterface::class)->getMock();
        $entity->expects($this->once())->method('getVersionControlField')->willReturn('version-number');
        $model = $this->getModel(1, ['version-number' => "a number"], $entity);
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        $this->expectException(InvalidDataTypeException::class);
        $this->object->onExecute($event);
    }

    public function testOnExecuteValueIsOutOfDate()
    {
        $field = 'version-number';
        $table = 'someTable';
        $systemVersionNumber = 1;
        $userVersionNumber = 0;
        $entity = $this->getMockBuilder(HasVersionControlIndexInterface::class)->getMock();
        $entity->expects($this->once())->method('getTable')->willReturn($table);
        $entity->expects($this->once())->method('getVersionControlField')->willReturn($field);
        
        $model = $this->getModel(1, [$field => $userVersionNumber], $entity);
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        $this->expectException(RecordOutOfDateException::class);
        $this->object->setSelect($this->getMockSelect($field, $table, $systemVersionNumber))->onExecute($event);        
    }

    public function testOnExecuteIsGood()
    {
        $field = 'version-number';
        $table = 'someTable';
        $systemVersionNumber = 10;
        $userVersionNumber = 10;
        $entity = $this->getMockBuilder(HasVersionControlIndexInterface::class)->getMock();
        $entity->expects($this->once())->method('getTable')->willReturn($table);
        $entity->expects($this->once())->method('getVersionControlField')->willReturn($field);
        
        $model = $this->getModel(1, [$field => $userVersionNumber], $entity);
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->assertTrue($this->object->setSelect($this->getMockSelect($field, $table, $systemVersionNumber))->onExecute($event));
        $this->assertEquals($userVersionNumber + 1,$model->getData()[$field]);
    }

    public function testValidateFieldExistsFieldMissing()
    {
        $data = ['desc' => 'some value'];
        $this->expectException(RequiredFieldMissingException::class);
        $this->object->validateFieldExists($data, 'record-version');
    }

    public function testValidateFieldExistsFieldPresent()
    {
        $data = ['desc' => 'some value', 'record-version' => 1];
        $this->assertTrue($this->object->validateFieldExists($data, 'record-version'));
    }

    /**
     * @dataProvider validateFieldTypeDataProvider
     */
    public function testValidateFieldType($value, $result)
    {
        if(!$result){
            $this->expectException(InvalidDataTypeException::class);
            $this->object->validateFieldType($value);
        }else{
            $this->assertTrue($this->object->validateFieldType($value));
        }
    }

    public function validateFieldTypeDataProvider()
    {
        return [
            ['value', false],
            [null, false],
            [new DateTime(), false],
            [null, false],
            [1, true],
            ["1", true],
            ["1-test", false],
        ];
    }

    /**
     * @dataProvider incrementDataProvider
     */
    public function testIncrement($value, $result)
    {
        $this->assertEquals($result, $this->object->increment($value));
    }

    public function incrementDataProvider()
    {
        return [
            [12, 13],
            [1, 2],
            [3, 4],
            [5, 6],
            [6, 7],
            [7, 8],
            [9, 10],
            [11, 12],
            [42, 43],
            [14, 15],
            [15, 16],
            [99, 100],
            [null, 1],
            ['a', 'b'],
        ];
    }

    public function testAdapter()
    {
        $this->assertSame($this->adapter, $this->object->setAdapter($this->adapter)->getAdapter());
    }

    public function testSelect()
    {
        $select = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($select, $this->object->setSelect($select)->getSelect());
    }

    public function testSelectLazy()
    {
        $this->assertInstanceOf(Select::class, $this->object->getSelect());
    }

    public function testValidateValueIsCurrentGood()
    {
        $this->assertTrue($this->runTestValidateValueIsCurrent(10, 10));
    }

    public function testValidateValueIsCurrentBad()
    {
        $this->expectException(RecordOutOfDateException::class);
        $this->assertTrue($this->runTestValidateValueIsCurrent(10, 11));
    }

    public function runTestValidateValueIsCurrent($systemVersionNumber, $userVersionNumber)
    {
        $idValue = 1;
        $table = 'test';
        $field = 'version_number';
        $data = [];
        $entity = $this->getMockBuilder(HasVersionControlIndexInterface::class)->getMock();
        $entity->expects($this->once())->method('getTable')->willReturn($table);

        $model = $this->getModel($idValue, $data, $entity);

        $select = $this->getMockSelect($field, $table, $systemVersionNumber);

        return $this->object->setSelect($select)->validateValueIsCurrent($model, $field, $userVersionNumber);
    }

    public function getMockSelect($field, $table, $systemVersionNumber)
    {
        $select = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();
        $select->expects($this->once())->method('setFetchFlat')->willReturnSelf();
        $select->expects($this->once())->method('setFetchStyle')->with(\PDO::FETCH_ASSOC | \PDO::FETCH_COLUMN)->willReturnSelf();
        $select->expects($this->once())->method('setFields')->with([$field])->willReturnSelf();
        $select->expects($this->once())->method('setTable')->with($table)->willReturnSelf();
        $select->expects($this->once())->method('setWhere')->willReturnSelf();
        $select->expects($this->once())->method('execute')->with($this->object->getAdapter())->willReturn($systemVersionNumber);
        return $select;
    }
}
