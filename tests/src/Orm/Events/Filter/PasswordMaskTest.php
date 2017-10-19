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

use DSchoenbauer\Orm\Entity\HasPasswordInterface;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Events\Filter\PasswordMask\PasswordMaskStrategyInterface;
use DSchoenbauer\Orm\ModelInterface;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of PasswordMaskTest
 *
 * @author David Schoenbauer
 */
class PasswordMaskTest extends TestCase
{

    private $object;

    protected function setUp()
    {
        $this->object = new PasswordMask();
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

        $passwordMask = $this->getMockBuilder(PasswordMaskStrategyInterface::class)->getMock();

        $entity = $this->getMockBuilder(HasPasswordInterface::class)->getMock();
        $entity->expects($this->any())->method('getPasswordMaskStrategy')->willReturn($passwordMask);


        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getEntity')->willReturn($entity);
        $model->expects($this->once())->method('getData')->willReturn(['test' => 'test']);
        $model->expects($this->once())->method('setData');

        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);



        $this->assertTrue($this->object->onExecute($event));
    }

    public function testObfascateDataPresent()
    {
        $data = ['name' => 'bob', 'password' => 'test'];
        $dataResult = ['name' => 'bob', 'password' => 'secret'];
        $field = 'password';
        $passwordMasker = $this->getMockBuilder(PasswordMaskStrategyInterface::class)->getMock();
        $passwordMasker->expects($this->once())->method('hashString')->with('test')->willReturn('secret');
        $this->assertEquals($dataResult, $this->object->obfascateData($data, $field, $passwordMasker));
    }

    public function testObfascateDataMissing()
    {
        $data = ['name' => 'bob'];
        $field = 'password';
        $passwordMasker = $this->getMockBuilder(PasswordMaskStrategyInterface::class)->getMock();
        $passwordMasker->expects($this->never())->method('hashString');
        $this->assertEquals($data, $this->object->obfascateData($data, $field, $passwordMasker));
    }
}
