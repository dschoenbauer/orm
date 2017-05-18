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
namespace DSchoenbauer\Orm\Events\Persistence\Http;

use DSchoenbauer\Orm\Entity\IsHttpInterface;
use DSchoenbauer\Orm\Events\Persistence\Http\DataExtract\DataExtractorFactory;
use DSchoenbauer\Orm\Events\Persistence\Http\DataExtract\DataExtractorInterface;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\EventManager\EventInterface;
use Zend\Http\Client;
use Zend\Http\Response;

/**
 * Description of AbstractHttpEventTest
 *
 * @author David Schoenbauer
 */
class AbstractHttpEventTest extends TestCase
{
    /* @var $object AbstractHttpEvent */

use TestModelTrait;

    protected $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(AbstractHttpEvent::class);
    }

    public function testMethod()
    {
        $this->assertNull($this->object->getMethod());
        $this->assertEquals("test", $this->object->setMethod('test')->getMethod());
    }

    public function testMethodOnConstructor()
    {
        $this->object = $this->getMockForAbstractClass(AbstractHttpEvent::class, [[], 0, null, "test"]);
        $this->assertEquals("test", $this->object->getMethod());
    }

    public function testClientLazyLoad()
    {
        $this->assertInstanceOf(Client::class, $this->object->getClient());
    }

    public function testDataExtractorFactoryLazyLoad()
    {
        $this->assertInstanceOf(DataExtractorFactory::class, $this->object->getDataExtractorFactory());
    }

    public function testDataExtractorFactory()
    {
        $dataExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock();
        $this->assertSame($dataExtractor, $this->object->setDataExtractorFactory($dataExtractor)->getDataExtractorFactory());
    }

    public function testGetData()
    {
        $data = ['test' => 'someValue', 'light' => 'dark'];
        $result = ['id' => 1, 'test' => 'someValue', 'light' => 'dark'];
        $model = $this->getModel(1, $data, $this->getAbstractEntity('id'));
        $this->assertEquals($result, $this->object->getData($model));
    }

    public function testGetUri()
    {
        $entity = $this->getMockBuilder(IsHttpInterface::class)->getMock();
        $entity->expects($this->any())->method('getEntityUrl')->willReturn('test');

        $this->assertEquals('test', $this->object->getUri($entity));
    }

    public function testBuildUri()
    {
        $this->object->buildUri($this->getModel(1, ['id' => 1], $this->getIsHttp('id', 'http://test.com')));
    }

    public function testBuildUriBadEntity()
    {
        $this->assertEquals(null, $this->object->buildUri($this->getModel(1, ['id' => 1], null)));
    }

    public function testOnExecuteNotCorrectTarget()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(1))->method('getTarget');
        $this->object->onExecute($event);
    }

    public function testOnExecuteNotCorrectEntity()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(1))
            ->method('getTarget')
            ->willReturn($this->getModel(0, [], new stdClass()));
        $this->object->onExecute($event);
    }

    public function testOnExecuteNotCorrectEntityChild()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(1))
            ->method('getTarget')
            ->willReturn($this->getModel(0, [], $this->getAbstractEntity('id')));
        $this->object->onExecute($event);
    }

    public function testOnExecuteCorrectEntity()
    {
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->exactly(1))
            ->method('getTarget')
            ->willReturn($this->getModel(0, [], $this->getIsHttp('id')));
        $this->object->onExecute($event);
    }

    public function testCheckForErrorIsError()
    {
        $response = $this->getResponse();
        $this->assertSame($response, $this->object->checkForError($response));
    }

    public function testCheckForErrorNoError()
    {
        $this->expectException(\DSchoenbauer\Orm\Exception\HttpErrorException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage("some body");
        $this->assertTrue($this->object->checkForError($this->getResponse(false, 500, "some body")));
    }

    public function getResponse($isSuccess = true, $statusCode = 200, $body = 'ok')
    {
        $response = $this->getMockBuilder(Response::class)->getMock();
        $response->expects($this->any())->method('isSuccess')->willReturn($isSuccess);
        $response->expects($this->any())->method('getStatusCode')->willReturn($statusCode);
        $response->expects($this->any())->method('getBody')->willReturn($body);
        return $response;
    }
}
