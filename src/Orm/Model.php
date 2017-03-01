<?php

/**
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

namespace DSchoenbauer\Orm;

/**
 * Description of Model
 *
 * @author David Schoenbauer
 */
class Model {

    private $_id;
    private $_data;
    private $_attributes;

    use \Zend\EventManager\EventManagerAwareTrait;

    public function __construct() {
        $this->setAttributes(new Framework\AttributeCollection());
    }
    
    function accept(VisitorInterface $visitor) {
        $visitor->visitModel($this);
        return $this;
    }

    function getId() {
        return $this->_id;
    }

    function getData() {
        return $this->_data;
    }

    function setId($id) {
        $this->_id = $id;
        return $this;
    }

    function setData($data) {
        $this->_data = $data;
        return $this;
    }

    /**
     * @return Framework\AttributeCollection
     */
    public function getAttributes() {
        return $this->_attributes;
    }

    public function setAttributes(Framework\AttributeCollection $attributes) {
        $this->_attributes = $attributes;
        return $this;
    }
}