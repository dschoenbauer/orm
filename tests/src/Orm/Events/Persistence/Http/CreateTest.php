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

use DSchoenbauer\Tests\Orm\Events\Persistence\Http\DataExtract\TestResponseTrait;
use DSchoenbauer\Tests\Orm\Events\Persistence\Http\TestModelTrait;
use PHPUnit\Framework\TestCase;
use Zend\Http\Client;
use Zend\Http\Request;

/**
 * Description of CreateTest
 *
 * @author David Schoenbauer
 */
class CreateTest extends TestCase
{

    protected $object;

    use TestModelTrait;
    use TestResponseTrait;

    protected function setUp()
    {
        $this->object = new Create();
    }

    public function testMethod()
    {
        $this->assertEquals(Request::METHOD_POST, $this->object->getMethod());
    }

    public function testGetUri()
    {
        $this->assertEquals('BobsYourUncle', $this->object->getUri($this->getIsHttp('id', 'BobsYourAunt', 'BobsYourUncle')));
    }

    public function testCrossFilled()
    {
        $model = $this->getModel(0, ['id' => 1999, 'name' => 'Dan', 'email' => 'dan@danco.com'], $this->getAbstractEntity('id'));
        $model->expects($this->once())->method('setId')->with(1999);
        $this->object->crossFillId($model);
    }

    public function testRun()
    {
        $data = ['id' => 1999, 'test' => 100];

        $response = $this->getResponse('something/json', \json_encode($data));
        $response->expects($this->once())->method('isSuccess')->willReturn(true);

        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())->method('setUri')->with('bobsYourUncle')->willReturnSelf();
        $client->expects($this->once())->method('setParameterPost')->with($data)->willReturnSelf();
        $client->expects($this->once())->method('setMethod')->with(Request::METHOD_POST)->willReturnSelf();
        $client->expects($this->once())->method('send')->willReturn($response);

        $model = $this->getModel(0, $data, $this->getIsHttp('id', 'bobsYourAunt', 'bobsYourUncle'));
        $model->expects($this->once())->method('setId')->with(1999);

        $this->object->setClient($client)->run($model);
    }
}
