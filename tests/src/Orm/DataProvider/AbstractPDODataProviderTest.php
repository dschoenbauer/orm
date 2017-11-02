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

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

/**
 * Description of TestAbstractPDODataProvider
 *
 * @author David Schoenbauer
 */
class AbstractPDODataProviderTest extends TestCase
{

    private $object;
    private $pdo;

    protected function setUp()
    {
        $this->pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->object = $this->getMockForAbstractClass(AbstractPDODataProvider::class, [$this->pdo]);
    }

    public function testIsDataProvider()
    {
        $this->assertInstanceOf(DataProviderInterface::class, $this->object);
    }

//  For later    
//    public function testIsAbstractClass(){
//        $reflection = new \ReflectionClass($this->object);
//        var_dump($reflection);
//        $this->assertTrue($reflection->isAbstract());
//    }

    public function testFetchFlat()
    {
        $this->assertFalse($this->object->getFetchFlat());
        $this->assertTrue($this->object->setFetchFlat(true)->getFetchFlat());
        $this->assertFalse($this->object->setFetchFlat(false)->getFetchFlat());
        $this->assertTrue($this->object->setFetchFlat()->getFetchFlat());
    }

    public function testFetchStyle()
    {
        $test = PDO::FETCH_CLASSTYPE;
        $this->assertEquals(PDO::FETCH_ASSOC, $this->object->getFetchStyle());
        $this->assertEquals($test, $this->object->setFetchStyle($test)->getFetchStyle());
    }

    public function testAdapter()
    {
        $pdo = $this->getMockBuilder(PDO::class)->disableOriginalConstructor()->getMock();
        $this->assertSame($this->pdo, $this->object->getAdapter());
        $this->assertSame($pdo, $this->object->setAdapter($pdo)->getAdapter());
    }

    public function testParameters()
    {
        $data = ['id' => 100];
        $this->assertNull($this->object->getParameters());
        $this->assertEquals($data, $this->object->setParameters($data)->getParameters());
    }

    public function testDefaultValues()
    {
        $value = "test data";
        $this->assertEquals([], $this->object->getDefaultValue());
        $this->assertEquals($value, $this->object->setDefaultValue($value)->getDefaultValue());
    }

    public function testGetDataNoParametersIsError()
    {
        $sql = "test";
        $result = "default value";
        $this->attachStatementMock($sql, $this->getStatementMock(null, null, null, null, true));
        $this->assertEquals($result, $this->object->setDefaultValue($result)->getData());
    }

    public function testGetDataParametersIsError()
    {
        $sql = "test";
        $result = "default value";
        $this->attachStatementMock($sql, $this->getStatementMock(null, null, null, ['bob'], true));
        $this->assertEquals($result, $this->object->setParameters(['bob'])->setDefaultValue($result)->getData());
    }

    public function testGetDataFetchFlat()
    {
        $sql = "test";
        $result = "default value";
        $style = \PDO::FETCH_LAZY;
        $this->attachStatementMock($sql, $this->getStatementMock(false, $result, $style));
        $this->assertEquals($result, $this->object->setFetchFlat()->setFetchStyle($style)->getData());
    }

    public function testGetDataFetch()
    {
        $sql = "test";
        $result = "default value";
        $style = \PDO::FETCH_ASSOC;
        $this->attachStatementMock($sql, $this->getStatementMock(true, $result, $style));
        $this->assertEquals($result, $this->object->getData());
    }

    public function attachStatementMock($sql, \PDOStatement $stmt)
    {
        $this->object->expects($this->any())->method('getSql')->willReturn($sql);
        $this->pdo->expects($this->any())->method('prepare')->with($sql)->willReturn($stmt);
    }

    public function getStatementMock($fetchAll = true, $result = [], $mode = null, $parameters = null, $isError = false)
    {
        $stmt = $this->getMockBuilder(PDOStatement::class)->getMock();
        $stmt->expects($this->exactly($isError ? 0 : ($fetchAll ? 1 : 0) ))->method('fetchAll')->willReturn($result);
        $stmt->expects($this->exactly($isError ? 0 : ($fetchAll ? 0 : 1) ))->method('fetch')->willReturn($result);
        $stmt->expects($this->exactly($isError ? 0 : 1 ))->method('setFetchMode')->with($mode);
        $execute = $stmt->expects($this->exactly(1))->method('execute')->willReturn(!$isError);
        if ($parameters !== null) {
            $execute->with($parameters);
        }
        return $stmt;
    }
}
