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
namespace DSchoenbauer\Orm\Events\Persistence\Http\DataExtract;

use DSchoenbauer\Tests\Orm\Events\Persistence\Http\DataExtract\TestResponseTrait;
use PHPUnit\Framework\TestCase;

/**
 * Description of JsonTest
 *
 * @author David Schoenbauer
 */
class JsonTest extends TestCase
{

    use TestResponseTrait;

    protected $object;

    protected function setUp()
    {
        $this->object = new Json();
    }

    /**
     * @dataProvider matchDataProvider
     * @param type $contentHeader
     * @param type $result
     */
    public function testMatch($contentHeader, $result)
    {
        $this->assertEquals($result, $this->object->match($this->getResponse($contentHeader)));
    }

    public function matchDataProvider()
    {
        return [
            'application/json' => ["application/json", true],
            'application/ld+json' => ["application/ld+json", true],
            'application/vnd.api+json' => ["application/vnd.api+json", true],
            'application/json; charset=UTF-8' => ["application/json; charset=UTF-8", true],
            'application/x-resource+json' => ["application/x-resource+json", true],
            'application/x-collection+json' => ["application/x-collection+json", true],
            'text/html' => ["text/html", false],
            'application/xhtml+xml' => ['application/xhtml+xml', false],
            'application/xml' => ['application/xml', false],
            'text/xml' => ['text/xml', false],
        ];
    }

    function testMatchFail()
    {
        $this->assertFalse($this->object->match($this->getResponse('application/json', '', 200, true)));
    }

    /**
     * @dataProvider extractDataProvider
     * @param type $json
     * @param type $result
     */
    public function testExtract($json, $result)
    {

        $this->assertEquals($result, $this->object->extract($this->getResponse(null, $json)));
    }

    public function extractDataProvider()
    {
        return [
            "Normal Json" => ['{"id":1}', ["id" => 1]],
            "Not Json" => ['{id:1}', null],
            "Malformed Json" => ['{"key": "<div class="item">an item
                    with newlines <span class="attrib"> and embedded
                    </span>html</div>"}', null],
            "Complex Data" => [
                '{"test":{"value":{"deep":true}}}',
                ['test' => ['value' => ['deep' => true]]]
            ],
            "xml" => [
                "<food><name>Belgian Waffles</name><price>$5.95</price><description>
                Two of our famous Belgian Waffles with plenty of real maple syrup
                </description><calories>650</calories></food>",
                null
            ]
        ];
    }
}
