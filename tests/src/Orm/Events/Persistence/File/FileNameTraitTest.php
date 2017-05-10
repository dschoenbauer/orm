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
use DSchoenbauer\Orm\Exception\InvalidPathException;
use PHPUnit\Framework\TestCase;

/**
 * Description of FileNameTraitTest
 *
 * @author David Schoenbauer
 */
class FileTraitTest extends TestCase
{

    /**
     * @var FileNameTrait
     */
    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForTrait(FileTrait::class);
    }

    public function testLoadFile()
    {
        $path = dirname(__FILE__);
        $this->assertEquals(['test' => true], $this->object->setPath($path)->loadFile($this->getEntity('test')));
    }

    public function testLoadFileBadJson()
    {
        $path = dirname(__FILE__);
        $this->assertEquals([], $this->object->setPath($path)->loadFile($this->getEntity('broke')));
    }

    public function testSaveFile()
    {
        $path = dirname(__FILE__);
        $data = ['test' => true];
        $result = '{"test":true}';
        $entity = $this->getEntity('save');
        $this->assertTrue($this->object->setPath($path)->saveFile($data, $entity));
        $contents = file_get_contents($this->object->getFileName($entity));
        $this->assertEquals($result, $contents);
    }

    public function testLoadFileFileNotFound()
    {
        $this->assertEquals([], $this->object->loadFile($this->getEntity('not-a-real-file')));
    }

    public function testGetFileName()
    {
        $table = 'Test';
        $this->assertEquals('.' . DIRECTORY_SEPARATOR . $table . '.json', $this->object->getFileName($this->getEntity($table)));
    }

    public function testGetFileNameNewExten()
    {
        $table = 'Test';
        $this->assertEquals('.' . DIRECTORY_SEPARATOR . $table . '.txt', $this->object->getFileName($this->getEntity($table), 'txt'));
    }

    public function testPath()
    {
        $path = "." . DIRECTORY_SEPARATOR;
        $this->assertEquals($path, $this->object->getPath());
        $this->assertEquals($path, $this->object->setPath($path)->getPath());
    }

    public function testPathAddTailSeperator()
    {
        $path = ".";
        $result = "." . DIRECTORY_SEPARATOR;
        $this->assertEquals($result, $this->object->setPath($path)->getPath());
    }

    public function testPathAddTailSeperatorIsCorrect()
    {
        $this->assertEquals("." . DIRECTORY_SEPARATOR, $this->object->setPath("./")->getPath());
        $this->assertEquals("." . DIRECTORY_SEPARATOR, $this->object->setPath(".\\")->getPath());
    }

    public function testPathIsADir()
    {
        $this->expectException(InvalidPathException::class);
        $this->assertEquals("." . DIRECTORY_SEPARATOR, $this->object->setPath("./")->getPath());
        $this->object->setPath("." . DIRECTORY_SEPARATOR . "notAPath");
    }

    protected function getEntity($table)
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getTable')->willReturn($table);
        return $entity;
    }

    protected function tearDown()
    {
        $path = dirname(__FILE__);
        @unlink($path . DIRECTORY_SEPARATOR . 'save.json');
    }
}
