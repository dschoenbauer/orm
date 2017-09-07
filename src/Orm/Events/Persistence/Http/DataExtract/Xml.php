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

use DSchoenbauer\Orm\Exception\InvalidXmlException;
use DSchoenbauer\Orm\Framework\XmlToArrayParser;
use Zend\Http\Response;

/**
 * Checks and extracts a response object for XML and converts it to a PHP array
 *
 * @author David Schoenbauer
 */
class Xml implements DataExtractorInterface
{

    public function extract(Response $response)
    {
        try {
            $parser = new XmlToArrayParser();
            return $parser->convert($response->getBody());
        } catch (InvalidXmlException $exc) {
            return [];
        }
    }

    public function match(Response $response)
    {
        return strpos(strtolower($response->getHeaders()->get('Content-type')->getFieldValue()), "xml") !== false;
    }
}
