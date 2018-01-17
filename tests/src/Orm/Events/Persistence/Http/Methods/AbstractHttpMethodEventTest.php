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
namespace DSchoenbauer\Orm\Events\Persistence\Http\Methods;

use DSchoenbauer\Orm\Entity\IsHttpInterface;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Events\Persistence\Http\Client\ClientVisitorInterface;
use DSchoenbauer\Orm\Events\Persistence\Http\DataExtract\DataExtractorFactory;
use DSchoenbauer\Orm\Exception\HttpErrorException;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\DataExtract\TestResponseTrait;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\EventInterface;
use Zend\Http\Client;

/**
 * Description of AbstractHttpMethodTest
 *
 * @author David Schoenbauer
 */
class AbstractHttpMethodEventTest extends TestCase
{

    use TestModelTrait;
    use TestResponseTrait;
    /**
     *
     * @var  AbstractHttpMethodEvent $object
     */
    private $object;

    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass(AbstractHttpMethodEvent::class,[[],'']);
    }

    public function testOnExecuteNoGood(){
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $this->object->expects($this->exactly(0))->method('send');
        $this->assertNull($this->object->onExecute($event));
    }
    
    public function testOnExecuteGood(){
        $entity = $this->getMockBuilder(IsHttpInterface::class)->getMock();
        $model = $this->getModel(0,[],$entity);
        
        $event = $this->getMockBuilder(EventInterface::class)->getMock();
        $event->expects($this->any())->method('getTarget')->willReturn($model);
        
        $this->object->expects($this->exactly(1))->method('send')->with($model);
        $this->object->expects($this->once())->method('getMethod')->willReturn('GET');
        $this->assertNull($this->object->onExecute($event));
    }
    
    public function testAccept(){
        $client = $this->getMockBuilder(Client::class)->getMock();
        $visitor = $this->getMockBuilder(ClientVisitorInterface::class)->getMock();
        $visitor->expects($this->once())->method('visitClient')->with($client);
        $this->object->setClient($client)->accept($visitor);
    }

    public function testIsEvent()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->object);
    }

    public function testClientLazyLoad()
    {
        $this->assertInstanceOf(Client::class, $this->object->getClient());
    }
    
    public function testClientLoad()
    {
        $clientMock = $this->getMockBuilder(Client::class)->getMock();
        $this->assertSame($clientMock, $this->object->setClient($clientMock)->getClient());
    }

    public function testDataExtractorFactoryLazyLoad()
    {
        $this->assertInstanceOf(DataExtractorFactory::class, $this->object->getDataExtractorFactory());
    }

    public function testUriMask()
    {
        $this->assertEquals('test', $this->object->setUriMask('test')->getUriMask());
        $this->assertEquals('test-again', $this->object->setUriMask('test-again')->getUriMask());
    }

    public function testGetUriNoParameter()
    {
        $this->assertEquals('test', $this->object->setUriMask('test')->getUri());
    }

    /**
     * @dataProvider getUriDataProvider
     * @param string $result
     * @param string $mask
     * @param array $data
     */
    public function testGetUri($result, $mask, array $data)
    {
        $this->assertEquals($result, $this->object->setUriMask($mask)->getUri($data));
    }

    public function getUriDataProvider()
    {
        return [
            'Null Test' => [null, null, []],
            'Too much data' => ['cat/1', 'cat/{id}', ['id' => 1, 'project-id' => 2]],
            'Too little data' => ['cat/{id}', 'cat/{id}', ['project-id' => 2]],
            '1 parameter' => ['category/123', 'category/{id}', ['id' => 123]],
            '2 parameter' => ['project/2/cat/1', 'project/{project-id}/cat/{id}', ['id' => 1, 'project-id' => 2]],
            'repeated parameter' => ['4/4/4','{project-id}/{project-id}/{project-id}',  ['project-id' => 4]],
        ];
    }
    
    
    public function testCheckForErrorIsError()
    {
        $response = $this->getResponse("someHeader");
        $this->assertSame($response, $this->object->checkForError($response));
    }

    public function testCheckForErrorNoError()
    {
        $this->expectException(HttpErrorException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage("some body");
        $this->assertTrue($this->object->checkForError($this->getResponse("","some body", 500)));
    }
}
