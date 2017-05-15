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
namespace DSchoenbauer\Orm\Events\Persistence\File;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Exception\RecordNotFoundException;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;

/**
 * Description of UpdateTest
 *
 * @author David Schoenbauer
 */
class UpdateTest extends TestCase
{

    protected $object;
    private $testPath;

    use TestModelTrait;

    protected function setUp()
    {
        $testFileTemplate = 'update.json.orig';
        $testFile = 'update.json';
        $this->testPath = str_replace('/', DIRECTORY_SEPARATOR, dirname(__FILE__) . '/../../../../../files/');
        copy($this->testPath . $testFileTemplate, $this->testPath . $testFile);
        $this->object = new Update();
    }

    protected function tearDown()
    {
        $testFile = 'update.json';
        @unlink($this->testPath . $testFile);
    }

    public function testOnExecuteNoModel()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteModelNoEntity()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($this->getModel(0, [], null));
        $this->assertFalse($this->object->onExecute($event));
    }

    public function testOnExecuteRecordNotFound()
    {
        $this->expectException(RecordNotFoundException::class);
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($this->getModel(0, [], $entity));
        $this->object->onExecute($event);
    }

    public function testProcessAction()
    {
        $record = ["id" => 0, "name" => "Andy"];
        $results = [
            ["id" => 0, "name" => "Andy"],
            ["id" => 1, "name" => "Wayne"],
            ["id" => 2, "name" => "Michael"],
            ["id" => 3, "name" => "Debra"],
            ["id" => 4, "name" => "Paul"]
        ];
        $this->object->setPath($this->testPath);
        $model = $this->getModel(0, $record, $this->getAbstractEntity('id', 'update'));
        $existingData = $this->object->loadFile($model->getEntity());
        $this->object->processAction($model, $existingData);
        $this->assertEquals($results, $this->object->loadFile($model->getEntity()));
    }
}
