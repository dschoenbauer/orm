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
 * Description of DataExtractorFactoryTest
 *
 * @author David Schoenbauer
 */
class DataExtractorFactoryTest extends TestCase
{

    protected $object;

    use TestResponseTrait;

    protected function setUp()
    {
        $this->object = new DataExtractorFactory();
    }

    public function testGetExtractorsWithDefaults()
    {
        $this->assertEquals(2, count($this->object->getExtractors()));
    }

    public function testGetExtractorsWithNoDefaults()
    {
        $this->object = new DataExtractorFactory(false);
        $this->assertEquals(0, count($this->object->getExtractors()));
    }

    public function testAdd()
    {
        $this->object = new DataExtractorFactory(false);
        $this->assertEquals(0, count($this->object->getExtractors()));
        $dataExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock();
        $this->object->add($dataExtractor);
        $this->assertEquals(1, count($this->object->getExtractors()));
        $this->assertSame($dataExtractor, $this->object->getExtractors()[0]);
    }

    public function testGetData()
    {
        $this->object = new DataExtractorFactory(false);
        $this->object->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor(true, "Success"));
        
        $this->assertEquals("Success",$this->object->getData($this->getResponse("test")));
    }
    
    public function testGetDataNoMatch()
    {
        $this->object = new DataExtractorFactory(false);
        $this->object->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor())
            ->add($this->getDataExtractor());
        $this->assertNull($this->object->getData($this->getResponse("test")));
        
    }
    

    public function getDataExtractor($match = false, $data = null)
    {
        $dataExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock();
        $dataExtractor->expects($this->exactly($match ? 1 : 0))->method('extract')->willReturn($data);
        $dataExtractor->expects($this->exactly(1))->method('match')->willReturn($match);
        return $dataExtractor;
    }
}
