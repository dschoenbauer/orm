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
 * Description of SelectTest
 *
 * @author David Schoenbauer
 */
class SelectTest extends TestCase
{

    protected $object;

    use TestModelTrait;
    use TestResponseTrait;

    protected function setUp()
    {
        $this->object = new Select();
    }

    public function testGetMethod()
    {
        $this->assertEquals(Request::METHOD_GET, $this->object->getMethod());
    }

    public function testRun()
    {
        $data = ['test' => 1, 'id' => 1999];
        $model = $this->getModel(1999, $data, $this->getIsHttp('id', 'entity', 'collection'));
        $model->expects($this->once())->method('setData')->with($data);

        $client = $this->getMockBuilder(Client::class)->getMock();
        $client->expects($this->once())->method('setMethod')->with(Request::METHOD_GET)->willReturnSelf();
        $client->expects($this->once())->method('setUri')->with('entity')->willReturnSelf();
        $client->expects($this->once())->method('send')->willReturn($this->getResponse('somethingJson', json_encode($data)));

        $this->object->setClient($client)->run($model);
    }
}
