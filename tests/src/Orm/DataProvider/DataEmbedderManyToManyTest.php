<?php
/*
 * The MIT License
 *
 * Copyright 2018 David Schoenbauer.
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

/**
 * Description of DataEmbedderManyToManyTest
 *
 * @author David Schoenbauer
 */
class DataEmbedderManyToManyTest extends \PHPUnit\Framework\TestCase
{

    private $object;
    private $dpT;
    private $dpE;

    protected function setUp()
    {
        $this->dpE = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $this->dpT = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $this->object = new DataEmbedderManyToMany($this->dpT, 'idx', $this->dpE, 'id');
    }

    public function testGetData()
    {
        $embeddedData = [
            ['id' => 1, 'test' => true, 'color' => 'black'],
            ['id' => 1, 'test' => false, 'color' => 'white'],
            ['id' => 2, 'test' => true, 'color' => 'red']
        ];

        $targetData = [
            ['id' => 1, 'xid' => 1, 'name' => 'Jenny'],
            ['id' => 2, 'xid' => 1, 'name' => 'Jason'],
            ['id' => 3, 'xid' => 2, 'name' => 'Jon'],
        ];
        $result = [
            ['id' => 1, 'xid' => 1, 'name' => 'Jenny', 
                '_embedded' => [
                    'item' => [
                        ['id' => 1, 'test' => true, 'color' => 'black'],
                        ['id' => 1, 'test' => false, 'color' => 'white'],
                    ]
                ]
            ],
            ['id' => 2, 'xid' => 1, 'name' => 'Jason', '_embedded' => [ 'item' => [['id' => 1, 'test' => true, 'color' => 'black'],
                    ['id' => 1, 'test' => false, 'color' => 'white'],]]],
            ['id' => 3, 'xid' => 2, 'name' => 'Jon', '_embedded'=>[ 'item' => [['id' => 2, 'test' => true, 'color' => 'red']]]],
        ];
        $this->dpE->expects($this->any())->method('getData')->willReturn($embeddedData);
        $this->dpT->expects($this->any())->method('getData')->willReturn($targetData);
        $this->object->setEmbeddedLinkField('id')->setTargetLinkField('xid');
        $this->assertEquals($result, $this->object->getData());
    }

    public function testBuildBadDataIndex()
    {
        $data = [['id' => 1, 'test' => true]];
        $result = [];
        $field = 'idx';
        $dataProvider = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $dataProvider->expects($this->any())->method('getData')->willReturn($data);
        $this->assertEquals($result, $this->object->buildIndex($dataProvider, $field));
    }

    public function testBuildGoodDataIndex()
    {
        $field = 'id';
        $data = [
            ['id' => 1, 'test' => true, 'active' => false, 'passed' => true],
            ['id' => 2, 'test' => true, 'active' => false, 'passed' => true],
            ['id' => 3, 'test' => true, 'active' => false, 'passed' => true],
            ['id' => 4, 'test' => true, 'active' => false, 'passed' => false],
            ['id' => 1, 'test' => false, 'active' => true, 'passed' => true],
            ['id' => 1, 'test' => false, 'active' => false, 'passed' => false],
        ];
        $result = [
            1 => [
                ['id' => 1, 'test' => true, 'active' => false, 'passed' => true],
                ['id' => 1, 'test' => false, 'active' => true, 'passed' => true],
                ['id' => 1, 'test' => false, 'active' => false, 'passed' => false],
            ],
            2 => [
                ['id' => 2, 'test' => true, 'active' => false, 'passed' => true],
            ],
            3 => [
                ['id' => 3, 'test' => true, 'active' => false, 'passed' => true],
            ],
            4 => [
                ['id' => 4, 'test' => true, 'active' => false, 'passed' => false],
            ],
        ];
        $dataProvider = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $dataProvider->expects($this->any())->method('getData')->willReturn($data);
        $this->assertEquals($result, $this->object->buildIndex($dataProvider, $field));
    }

    public function testEmbeddedDataProvider()
    {
        $this->assertSame($this->dpE, $this->object->getEmbeddedDataProvider());

        $dp = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $this->assertSame($dp, $this->object->setEmbeddedDataProvider($dp)->getEmbeddedDataProvider());
    }

    public function testEmbeddedLinkField()
    {
        $this->assertEquals('id', $this->object->getEmbeddedLinkField());
        $result = "this is a test";
        $this->assertEquals($result, $this->object->setEmbeddedLinkField($result)->getEmbeddedLinkField());
    }

    public function testTargetDataProvider()
    {
        $this->assertSame($this->dpT, $this->object->getTargetDataProvider());


        $dp = $this->getMockBuilder(DataProviderInterface::class)->getMock();
        $this->assertSame($dp, $this->object->setTargetDataProvider($dp)->getTargetDataProvider());
    }

    public function testTargetLinkField()
    {
        $this->assertEquals('idx', $this->object->getTargetLinkField());
        $result = "this is a test";
        $this->assertEquals($result, $this->object->setTargetLinkField($result)->getTargetLinkField());
    }

    public function testName()
    {
        $result = 'someName';
        $this->assertEquals('item', $this->object->getName());
        $this->assertEquals($result, $this->object->setName($result)->getName());
    }

    public function testEmbeddedKey()
    {
        $result = 'items';
        $this->assertEquals('_embedded', $this->object->getEmbeddedKey());
        $this->assertEquals($result, $this->object->setEmbeddedKey($result)->getEmbeddedKey());
    }
}
