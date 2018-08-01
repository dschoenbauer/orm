<?php
/*
 * The MIT License
 *
 * Copyright 2018 David Schoenbauer.
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
namespace DSchoenbauer\Tests\Orm\Events\DataProvider;

use DSchoenbauer\Orm\DataProvider\DataProviderInterface;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;

/**
 * @author David Schoenbauer
 */
class MockDataProviderVisitor implements DataProviderInterface, VisitorInterface
{
    private $data = [];
    private $attributes = [];

    public function __construct($data, $attributes)
    {
        $this->setData($data)->setAttributes($attributes);
        
    }

    public function visitModel(ModelInterface $model)
    {
        foreach ($this->getAttributes() as $key => $value) {
            $model->getAttributes()->set($key, $value);
        }
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

}
