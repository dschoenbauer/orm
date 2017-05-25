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

use DSchoenbauer\Orm\Exception\HttpErrorException;
use DSchoenbauer\Orm\Framework\AttributeCollection;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\DataExtract\TestResponseTrait;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\Http\Client;
use Zend\Http\Request;

/**
 * Description of DeleteTest
 *
 * @author David Schoenbauer
 */
class DeleteTest extends TestCase
{

    protected $object;

    use TestResponseTrait;
    use TestModelTrait;

    protected function setUp()
    {
        $this->object = new Delete();
    }

    public function testGetMethod()
    {
        $this->assertEquals(Request::METHOD_DELETE, $this->object->getMethod());
    }

    public function testRun()
    {
        $response = $this->getResponse("");
        $attributes = $this->getMockBuilder(AttributeCollection::class)->getMock();
        $attributes->expects($this->once())->method('set')->with('response', $response);

        $model = $this->getModel(1998, [], $this->getIsHttp(null, 'bobsYouUncle', 'bobsYourAunt'));
        $model->expects($this->once())->method('getAttributes')->willReturn($attributes);

        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())->method('setUri')->with('bobsYouUncle')->willReturnSelf();
        $client->expects($this->once())->method('setMethod')->with(Request::METHOD_DELETE)->willReturnSelf();
        $client->expects($this->once())->method('send')->willReturn($response);

        $this->object->setClient($client)->run($model);
    }

    public function testRunFail()
    {
        $this->expectException(HttpErrorException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage("Test");
        $response = $this->getResponse("", "Test", 500);

        $model = $this->getModel(1998, [], $this->getIsHttp(null, 'bobsYouUncle', 'bobsYourAunt'));

        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())->method('setUri')->with('bobsYouUncle')->willReturnSelf();
        $client->expects($this->once())->method('setMethod')->with(Request::METHOD_DELETE)->willReturnSelf();
        $client->expects($this->once())->method('send')->willReturn($response);

        $this->object->setClient($client)->run($model);
    }
}
