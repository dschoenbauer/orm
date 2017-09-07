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
namespace DSchoenbauer\Tests\Orm\Events\Persistence\Http\DataExtract;

use Zend\Http\Header\HeaderInterface;
use Zend\Http\Headers;
use Zend\Http\Response;

/**
 * Description of TestResponseTrait
 *
 * @author David Schoenbauer
 */
trait TestResponseTrait
{

    public function getResponse($header, $body = "", $statusCode = 200)
    {
        $responseMock = $this->getMockBuilder(Response::class)->getMock();
        $headersMock = $this->getMockBuilder(Headers::class)->getMock();
        $headerMock = $this->getMockBuilder(HeaderInterface::class)->getMock();

        $headerMock->expects($this->any())
            ->method('getFieldValue')
            ->willReturn($header);

        $headersMock->expects($this->any())
            ->method('get')
            ->willReturn($headerMock);

        $responseMock->expects($this->any())
            ->method('getHeaders')
            ->willReturn($headersMock);

        $responseMock->expects($this->any())
            ->method('getBody')
            ->willReturn($body);

        $responseMock->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($statusCode);

        $responseMock->expects($this->any())
            ->method('isSuccess')
            ->willReturn(in_array($statusCode, range(200, 299)));

        return $responseMock;
    }
}
