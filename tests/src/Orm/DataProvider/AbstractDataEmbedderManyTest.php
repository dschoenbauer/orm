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
namespace DSchoenbauer\Orm\DataProvider;

use DSchoenbauer\Exception\Platform\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Description of DataEmbedderOneToManyTest
 *
 * @author David Schoenbauer
 */
class AbstractDataEmbedderManyTest extends TestCase
{

    /**
     * @var AbstractDataEmbedderMany
     */
    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(AbstractDataEmbedderMany::class);
    }

    public function testEnsureInterface()
    {
        $this->assertInstanceOf(DataProviderInterface::class, $this->object);
    }

    public function testEmbedKey()
    {
        $this->assertEquals('_embedded', $this->object->getEmbedKey());
        $this->assertEquals('test', $this->object->setEmbedKey('test')->getEmbedKey());
    }

    public function testName()
    {
        $this->assertEquals('item', $this->object->getName());
        $this->assertEquals('test', $this->object->setName('test')->getName());
    }

    public function testNoLinkField()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->object->getLinkField();
    }

    public function testLinkField()
    {
        $linkField = 'test';
        $this->assertSame($linkField, $this->object->setLinkField($linkField)->getLinkField());
    }

    public function testTargetDataProvider()
    {
        $dataProvider = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $this->assertSame($dataProvider, $this->object->setTargetDataProvider($dataProvider)->getTargetDataProvider());
    }

    public function testNoTargetDataProvider()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->object->getTargetDataProvider();
    }

    public function testEmbeddedDataProvider()
    {
        $dataProvider = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $this->assertSame($dataProvider, $this->object->setEmbeddedDataProvider($dataProvider)->getEmbeddedDataProvider());
    }

    public function testNoEmbeddedDataProvider()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->object->getEmbeddedDataProvider();
    }

    /**
     * @dataProvider prepDataDataProvider
     */
    public function testPrepData($expected, $embedKey, $name, $data, $defaultValue)
    {
        $this->assertEquals($expected, $this->object->setEmbedKey($embedKey)->setName($name)->prepData($data, $defaultValue));
    }

    public function prepDataDataProvider()
    {
        return [
            'normal' => [[['test'=>1,'_embedded'=>['test'=>[]]]],'_embedded','test',[['test'=>1]],[]],
            'existing embedded' => [[['test'=>1,'_embedded'=>['jon'=>[],'test'=>[]]]],'_embedded','test',[['test'=>1,'_embedded'=>['jon'=>[]]]],[]],
            'existing name' => [[['test'=>1,'_embedded'=>['test'=>[]]]],'_embedded','test',[['test'=>1]],[]],
        ];
    }
}
