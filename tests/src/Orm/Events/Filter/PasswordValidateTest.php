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
namespace DSchoenbauer\Orm\Events\Filter;

use DSchoenbauer\Exception\Http\ClientError\UnauthorizedException;
use DSchoenbauer\Orm\Entity\HasPasswordInterface;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Events\Filter\PasswordMask\PasswordMaskStrategyInterface;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Select;
use DSchoenbauer\Sql\Where\ArrayWhere;
use PDO;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;

/**
 * Description of PasswordValidateTest
 *
 * @author David Schoenbauer
 */
class PasswordValidateTest extends TestCase
{
    /* @var $object PasswordValidate */

    private $object;

    protected function setUp()
    {
        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = new PasswordValidate([], $pdo);
    }

    public function testHasProperLineage()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->object);
    }

    public function testExecuteNoModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testExecuteModelNoEntity()
    {
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->once())->method('getEntity');

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->assertFalse($this->object->onExecute($event));
    }

    public function testExecuteAllGood()
    {
        $this->object = $this->getMockBuilder(PasswordValidate::class)->setMethods(['validateUser'])->disableOriginalConstructor()->getMock();
        $this->object->expects($this->any())->method('validateUser')->willReturn(true);
        $passwordMask = $this->getMockBuilder(PasswordMaskStrategyInterface::class)->getMock();

        $entity = $this->getMockBuilder(HasPasswordInterface::class)->getMock();
        $entity->expects($this->any())->method('getPasswordMaskStrategy')->willReturn($passwordMask);

        $eventManager = $this->getMockBuilder(EventManager::class)->getMock();
        $eventManager->expects($this->atLeast(1))->method('trigger');

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getEntity')->willReturn($entity);
        $model->expects($this->once())->method('getData')->willReturn(['test' => 'test']);
        $model->expects($this->once())->method('getEventManager')->willReturn($eventManager);
        $model->expects($this->exactly(1))->method('setId')->with(10);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->assertTrue($this->object->onExecute($event));
    }

    public function testExecuteBadUser()
    {
        
        $this->object = $this->getMockBuilder(PasswordValidate::class)->setMethods(['validateUser'])->disableOriginalConstructor()->getMock();
        $this->object->expects($this->any())->method('validateUser')->willReturn(false);
        $passwordMask = $this->getMockBuilder(PasswordMaskStrategyInterface::class)->getMock();

        $entity = $this->getMockBuilder(HasPasswordInterface::class)->getMock();
        $entity->expects($this->any())->method('getPasswordMaskStrategy')->willReturn($passwordMask);


        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getEntity')->willReturn($entity);
        $model->expects($this->once())->method('getData')->willReturn(['test' => 'test']);

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);

        $this->expectException(UnauthorizedException::class);
        $this->object->onExecute($event);
    }

    public function testValidateUserDataNotPresent()
    {
        $passwordInfo = $this->getMockBuilder(HasPasswordInterface::class)->getMock();
        $passwordInfo->expects($this->any())->method('getUserNameField')->willReturn('user');
        $passwordInfo->expects($this->any())->method('getPasswordField')->willReturn('pass');


        $this->assertFalse($this->object->validateUser(null, $passwordInfo), 'No user info');
        $this->assertFalse($this->object->validateUser([], $passwordInfo), 'No user info');
        $this->assertFalse($this->object->validateUser(['name' => 'bob'], $passwordInfo), 'No user info');
        $this->assertFalse($this->object->validateUser(['pass' => 'test'], $passwordInfo), 'No user');
        $this->assertFalse($this->object->validateUser(['user' => 'test'], $passwordInfo), 'No password');
    }

    public function testValidateUserAll()
    {

        $nullObj = new \stdClass();
        $nullObj->id = 100;
        $nullObj->hash = null;
        $this->object = $this->getMockBuilder(PasswordValidate::class)->setMethods(['getPasswordMetaData'])->disableOriginalConstructor()->getMock();
        $this->object->expects($this->any())->method('getPasswordMetaData')->willReturn($nullObj);

        $passwordMaskStrategy = $this->getMockBuilder(PasswordMaskStrategyInterface::class)->getMock();
        $passwordMaskStrategy->expects($this->once())->method('validate')->willReturn(true);

        $passwordInfo = $this->getMockBuilder(HasPasswordInterface::class)->getMock();
        $passwordInfo->expects($this->any())->method('getUserNameField')->willReturn('user');
        $passwordInfo->expects($this->any())->method('getPasswordField')->willReturn('pass');
        $passwordInfo->expects($this->any())->method('getPasswordMaskStrategy')->willReturn($passwordMaskStrategy);

        $this->assertEquals(100, $this->object->validateUser(['user' => 'test', 'pass' => 'test'], $passwordInfo));
    }

        public function testValidateUserFail()
    {

        $nullObj = new \stdClass();
        $nullObj->id = 100;
        $nullObj->hash = null;
        $this->object = $this->getMockBuilder(PasswordValidate::class)->setMethods(['getPasswordMetaData'])->disableOriginalConstructor()->getMock();
        $this->object->expects($this->any())->method('getPasswordMetaData')->willReturn($nullObj);

        $passwordMaskStrategy = $this->getMockBuilder(PasswordMaskStrategyInterface::class)->getMock();
        $passwordMaskStrategy->expects($this->once())->method('validate')->willReturn(false);

        $passwordInfo = $this->getMockBuilder(HasPasswordInterface::class)->getMock();
        $passwordInfo->expects($this->any())->method('getUserNameField')->willReturn('user');
        $passwordInfo->expects($this->any())->method('getPasswordField')->willReturn('pass');
        $passwordInfo->expects($this->any())->method('getPasswordMaskStrategy')->willReturn($passwordMaskStrategy);

        $this->assertFalse($this->object->validateUser(['user' => 'test', 'pass' => 'test'], $passwordInfo));
    }
    
    public function testAdapter()
    {
        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($pdo, $this->object->setAdapter($pdo)->getAdapter());
    }

    public function testSelect()
    {
        $mySelect = $this->getMockBuilder(Select::class)->disableOriginalConstructor()->getMock();

        $lazy = $this->object->getSelect();
        $this->assertInstanceOf(Select::class, $this->object->getSelect());
        $this->assertSame($lazy, $this->object->getSelect());

        $this->assertSame($mySelect, $this->object->setSelect($mySelect)->getSelect());
    }

    public function testGetPasswordMetaData()
    {
        $userName = "test";
        $table = 'table';
        $idField = 'idField';
        $passwordField = 'passwordField';
        $userNameField = 'userNameField';

        $nullReturn = new \stdClass();
        $nullReturn->hash = null;
        $nullReturn->id = null;
        
        $passwordInfo = $this->getMockBuilder(HasPasswordInterface::class)->getMock();
        $passwordInfo->expects($this->any())->method('getTable')->willReturn($table);
        $passwordInfo->expects($this->any())->method('getIdField')->willReturn($idField);
        $passwordInfo->expects($this->any())->method('getPasswordField')->willReturn($passwordField);
        $passwordInfo->expects($this->any())->method('getUserNameField')->willReturn($userNameField);

        $select = $this->getMockBuilder(Select::class)->disableOriginalConstructor()
            //->setMethods(['setTable','setFields','setWhere','setFetchFlat','setFetchStyle','setDefaultValue','execute'])
            ->getMock();
        $select->expects($this->atLeast(1))->method('setTable')->with($table)->willReturnSelf();
        $select->expects($this->atLeast(1))->method('setFields')->with([$passwordField . ' as hash', $idField . ' as id'])->willReturnSelf();
        $select->expects($this->atLeast(1))->method('setWhere')->with($this->isInstanceOf(ArrayWhere::class))->willReturnSelf(); //Needs to be better
        $select->expects($this->atLeast(1))->method('setFetchFlat')->willReturnSelf();
        $select->expects($this->atLeast(1))->method('setFetchStyle')->with(\PDO::FETCH_OBJ)->willReturnSelf();
        $select->expects($this->atLeast(1))->method('setDefaultValue')->with($nullReturn)->willReturnSelf();
        $select->expects($this->atLeast(1))->method('execute')->with($this->isInstanceOf(\PDO::class))->willReturn(true);

        $this->assertTrue($this->object->setSelect($select)->getPasswordMetaData($userName, $passwordInfo));
    }
    
    function testGetNullReturnDefault(){
        $nullReturn = new \stdClass();
        $nullReturn->id = null;
        $nullReturn->hash = null;
        $this->assertEquals($nullReturn, $this->object->getNullReturn());
    }
    
    function testGetNullReturnContent(){
        $dataReturn = new \stdClass();
        $dataReturn->id = 999999;
        $dataReturn->hash = 'someHash';
        $this->assertEquals($dataReturn, $this->object->getNullReturn(999999,'someHash'));
    }
    
}
