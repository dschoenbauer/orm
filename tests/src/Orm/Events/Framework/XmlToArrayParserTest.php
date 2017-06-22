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
namespace DSchoenbauer\Orm\Events\Framework;

use DSchoenbauer\Orm\Framework\XmlToArrayParser;
use PHPUnit\Framework\TestCase;

/**
 * Description of XmlToArrayParserTest
 *
 * @author David Schoenbauer
 */
class XmlToArrayParserTest extends TestCase
{

    /**
     * @var XmlToArrayParser
     */
    private $object;
    private $path;

    protected function setUp()
    {
        $path = ['..', '..', '..', '..', 'files', 'xml'];
        $this->path = dirname(__FILE__) . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR;
        $this->object = new XmlToArrayParser();
    }

    /**
     * @dataProvider getConvertDataProvider
     */
    public function testConvert($type)
    {
        $file = $type . '.xml';
        $resultFile = $type . '.json';

        $xml = $this->loadXml($file);
        $result = json_decode(file_get_contents($this->path . $resultFile), true);
        $this->assertEquals($result, $this->object->convert($xml));
    }

    public function testConvertError()
    {
        $this->expectException(\Exception::class);
        $this->object->convert($this->loadXml('error.xml'));
    }

    public function loadXml($file)
    {
        return file_get_contents($this->path . $file);
    }

    public function getConvertDataProvider()
    {
        return [
            'basic' => ['basic'],
            'complex' => ['complex'],
            'attribute' => ['attribute'],
        ];
    }
}
