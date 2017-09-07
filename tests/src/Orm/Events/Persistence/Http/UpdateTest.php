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
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\DataExtract\TestResponseTrait;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\Http\Client;
use Zend\Http\Request;

/**
 * Description of UpdateTest
 *
 * @author David Schoenbauer
 */
class UpdateTest extends TestCase
{

    use TestModelTrait;
    use TestResponseTrait;

    protected $object;

    protected function setUp()
    {
        $this->object = new Update();
    }

    public function testGetMethod()
    {
        $this->assertEquals(Request::METHOD_PUT, $this->object->getMethod());
    }

    public function testRun()
    {
        $data = ['id' => 1999, 'test' => 100];

        $response = $this->getResponse('something/json', \json_encode($data));

        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())->method('setUri')->with('bobsYourAunt')->willReturnSelf();
        $client->expects($this->once())->method('setParameterPost')->with($data)->willReturnSelf();
        $client->expects($this->once())->method('setMethod')->with(Request::METHOD_PUT)->willReturnSelf();
        $client->expects($this->once())->method('send')->willReturn($response);

        $model = $this->getModel(0, $data, $this->getIsHttp('id', 'bobsYourAunt', 'bobsYourUncle', true));
        $model->expects($this->once())->method('setData')->with($data);

        $this->object->setClient($client)->run($model);
    }

    public function testRunFail()
    {
        $data = ['id' => 1999, 'test' => 100];

        $this->expectException(HttpErrorException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage(\json_encode($data));

        $response = $this->getResponse('something/json', \json_encode($data), 500);

        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())->method('setUri')->with('bobsYourAunt')->willReturnSelf();
        $client->expects($this->once())->method('setParameterPost')->with($data)->willReturnSelf();
        $client->expects($this->once())->method('setMethod')->with(Request::METHOD_PUT)->willReturnSelf();
        $client->expects($this->once())->method('send')->willReturn($response);

        $model = $this->getModel(0, $data, $this->getIsHttp('id', 'bobsYourAunt', 'bobsYourUncle', true));
        $model->expects($this->exactly(0))->method('setData');

        $this->object->setClient($client)->run($model);
    }
}
