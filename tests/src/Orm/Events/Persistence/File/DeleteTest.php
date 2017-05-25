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

use DSchoenbauer\Orm\Exception\RecordNotFoundException;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;

/**
 * Description of DeleteTest
 *
 * @author David Schoenbauer
 */
class DeleteTest extends TestCase
{

    protected $object;
    private $testPath;

    use TestModelTrait;

    protected function setUp()
    {
        $testFileTemplate = 'delete.json.orig';
        $testFile = 'delete.json';
        $this->testPath = str_replace('/', DIRECTORY_SEPARATOR, dirname(__FILE__) . '/../../../../../files/');
        
        copy($this->testPath . $testFileTemplate, $this->testPath . $testFile);
        $this->object = new Delete();
    }

    protected function tearDown()
    {
        $testFile = 'delete.json';
        @unlink($this->testPath . $testFile);
    }

    public function testProcessActionFeil()
    {
        $model = $this->getModel(0, [], $this->getAbstractEntity('id'));
        $existingData = [];
        $this->expectException(RecordNotFoundException::class);
        $this->object->processAction($model, $existingData);
    }

    public function testProcessAction()
    {
        $model = $this->getModel(0, [], $this->getAbstractEntity('id', 'delete'));
        $existingData = [['data' => 'sure']];
        $this->assertTrue($this->object->setPath($this->testPath)->processAction($model, $existingData));
        $this->assertEquals([], $this->object->loadFile($model->getEntity()));
    }
}
