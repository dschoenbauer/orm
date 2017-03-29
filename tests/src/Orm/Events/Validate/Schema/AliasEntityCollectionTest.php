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

use DSchoenbauer\Orm\ModelInterface;
use PHPUnit\Framework\TestCase;

/**
 * Description of AliasEntityCollection
 *
 * @author David Schoenbauer
 */
class AliasEntityCollectionTest extends TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new AliasEntityCollection();
    }

    /**
     * @dataProvider getAliasRowDataProvider
     * @param type $data
     * @param type $aliases
     * @param type $results
     */
    public function testValidate($data, $aliases, $results)
    {
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->once())->method('setData')->willReturnCallback(function($data) {
            $this->_data = $data;
            return $this;
        });
        $model->expects($this->once())->method('getData')->willReturnCallback(function() {
            return $this->_data;
        });
        $this->object->setModel($model)->validate($data, $aliases);
        $this->assertEquals($results, $this->object->getModel()->getData());
    }

    public function getAliasRowDataProvider()
    {
        return [
            "No Alias" => [
                [
                    ['id' => 1, 'name' => 'test', 'ack' => true],
                    ['id' => 2, 'name' => 'test1', 'ack' => false],
                ],
                [],
                [
                    ['id' => 1, 'name' => 'test', 'ack' => true],
                    ['id' => 2, 'name' => 'test1', 'ack' => false],
                ],
            ],
            "Single Alias" => [
                [
                    ['id' => 1, 'name' => 'test'],
                    ['id' => 2, 'name' => 'test2'],
                ],
                ['id' => 'idx'],
                [
                    ['idx' => 1, 'name' => 'test'],
                    ['idx' => 2, 'name' => 'test2'],
                ]
            ],
            "All Alias" => [
                [
                    ['id' => 1, 'name' => 'test'],
                    ['id' => 2, 'name' => 'test2'],
                ],
                ['id' => 'idx', 'name' => 'fullName'],
                [
                    ['idx' => 1, 'fullName' => 'test'],
                    ['idx' => 2, 'fullName' => 'test2'],
                ]
            ],
            "No Data" => [[], ['id' => 'idx'], []],
        ];
    }
}