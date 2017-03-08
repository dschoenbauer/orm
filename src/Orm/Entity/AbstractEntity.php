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

namespace DSchoenbauer\Orm\Entity;

/**
 * Description of AbstractEntity
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
abstract class AbstractEntity implements EntityInterface {

    private $_idField;
    private $_table;
    private $_name;

    public function getIdField() {
        return $this->_idField;
    }

    public function getTable() {
        return $this->_table;
    }

    public function getName() {
        return $this->_name;
    }

    public function setIdField($idField) {
        $this->_idField = $idField;
        return $this;
    }

    public function setTable($table) {
        $this->_table = $table;
        return $this;
    }

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function getAllFields() {
        $fields = [];
        if ($this instanceof HasBoolFieldsInterface) {
            $fields = array_merge($fields, $this->getBoolFields());
        }
        if ($this instanceof HasDateFieldsInterface) {
            $fields = array_merge($fields, $this->getDateFields());
        }
        if ($this instanceof HasNumericFieldsInterface) {
            $fields = array_merge($fields, $this->getNumericFields());
        }
        if ($this instanceof HasStringFieldsInterface) {
            $fields = array_merge($fields, $this->getStringFields());
        }
        return $fields;
    }
}
