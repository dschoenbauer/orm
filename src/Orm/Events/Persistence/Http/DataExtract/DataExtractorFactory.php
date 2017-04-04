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

use Zend\Http\Response;

/**
 * Description of ResponseFactory
 *
 * @author David Schoenbauer
 */
class DataExtractorFactory
{
    protected $extractors = [];
    
    public function __construct($loadDefaults = true)
    {
        if($loadDefaults){
            $this->loadDefaults();
        }
    }
    
    public function loadDefaults(){
        $this->add(new Json());
    }


    public function getData(Response $response)
    {
        $extractors = $this->getExtractors();
        /* @var $extractor DataExtractorInterface */
        foreach ($extractors as $extractor) {
            if ($extractor->match($response)) {
                return $extractor->extract($response);
            }
        }
    }

    public function add(DataExtractorInterface $dataExtractor)
    {
        $this->extractors[] = $dataExtractor;
        return $this;
    }

    public function getExtractors()
    {
        return $this->extractors;
    }
}
