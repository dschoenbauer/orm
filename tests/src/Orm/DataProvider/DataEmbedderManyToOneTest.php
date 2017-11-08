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

use PHPUnit\Framework\TestCase;

/**
 * Description of DataEmbedderManyToOneTest
 *
 * @author David Schoenbauer
 */
class DataEmbedderManyToOneTest extends TestCase
{
    /* @var $$object DataEmbedderManyToOne */

    private $object;

    protected function setUp()
    {
        $this->object = new DataEmbedderManyToOne();
    }

    public function testParent()
    {
        $this->assertInstanceOf(AbstractDataEmbedderMany::class, $this->object);
        $this->assertInstanceOf(DataProviderInterface::class, $this->object);
    }

    /**
     * @dataProvider getData
     * @param type $expected
     * @param type $embedded
     * @param type $target
     */
    public function testGetData($expected, $target, $embedded, $link, $name)
    {
        $this->assertEquals($expected, $this->object
                ->setName($name)->setLinkField($link)
                ->setEmbeddedDataProvider($this->getDataProvider($embedded))
                ->setTargetDataProvider($this->getDataProvider($target))
                ->getData());
    }

    public function getData()
    {
        return [
            'value not in embedded' => [
                [
                    1 => [
                        'name' => 'test1',
                        'id' => 1,
                        'location' => 34,
                        '_embedded' => ['test' =>
                            ['id' => 34, 'name' => 'one']
                        ]],
                    2 => [
                        'name' => 'test2',
                        'id' => 2,
                        'location' => 10,
                        '_embedded' => [
                            'test' => null
                        ]
                    ],
                ],
                [
                    1 => ['name' => 'test1', 'id' => 1, 'location' => 34],
                    2 => ['name' => 'test2', 'id' => 2, 'location' => 10],
                ],
                [
                    34 => ['id' => 34, 'name' => 'one']
                ],
                'location',
                'test'
            ],
            'field not in target' => [
                [
                    1 => [
                        'name' => 'test1',
                        'id' => 1,
                        'location' => 34,
                        '_embedded' => ['test' =>
                            ['id' => 34, 'name' => 'one']
                        ]],
                    2 => [
                        'name' => 'test2',
                        'id' => 2,
                        
                        '_embedded' => [
                            'test' => null
                        ]
                    ],
                ],
                [
                    1 => ['name' => 'test1', 'id' => 1, 'location' => 34],
                    2 => ['name' => 'test2', 'id' => 2],
                ],
                [
                    34 => ['id' => 34, 'name' => 'one']
                ],
                'location',
                'test'
            ],
            'Normal test' => [
                [
                    1 => [
                        'name' => 'test1',
                        'id' => 1,
                        'location-id' => 45,
                        '_embedded' => ['location' => [
                                'id' => 45, 'name' => 'one'
                            ]
                        ]],
                    2 => [
                        'name' => 'test2',
                        'id' => 2,
                        'location-id' => 45,
                        '_embedded' => ['location' => [
                                'id' => 45, 'name' => 'one'
                            ]
                        ]],
                ],
                [
                    1 => ['name' => 'test1', 'id' => 1, 'location-id' => 45],
                    2 => ['name' => 'test2', 'id' => 2, 'location-id' => 45],
                ],
                [
                    45 => ['id' => 45, 'name' => 'one'],
                    46 => ['id' => 46, 'name' => 'one']
                ],
                'location-id',
                'location'
            ]
        ];
    }

    public function getDataProvider($data)
    {
        $mock = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $mock->expects($this->any())->method('getData')->willReturn($data);
        return $mock;
    }
}
