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
 * Provides common functionality all entities could use
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
abstract class AbstractEntity implements EntityInterface
{

    private $idField;
    private $table;
    private $name;

    /**
     * provides field with primary key
     * @return string
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * provides which table the entities data is stored in
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * provides a name that can be used to reference the entity
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * sets field with primary key
     * @param string $idField
     * @return $this
     */
    public function setIdField($idField)
    {
        $this->idField = $idField;
        return $this;
    }

    /**
     * sets which table the entities data is stored in
     * @param string $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * sets a name that can be used to reference the entity
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * provides an array of all fields the entity has
     * @return array
     */
    public function getAllFields()
    {
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
