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
namespace DSchoenbauer\Orm\Events\Validate\DataType;

use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Exception\InvalidDataTypeException;
use DSchoenbauer\Orm\Model;
use Zend\EventManager\Event;

/**
 * Framework for validating data types
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
abstract class AbstractDataType extends AbstractEvent
{

    /**
     * full name space of an interface that defines a given field type
     * @return string
     * @since v1.0.0
     */
    abstract public function getTypeInterface();

    /**
     * returns the fields affected by the entity interface
     * @param mixed $entity an entity object implements the getTypeInterface
     * @return array an array of fields that are relevant to the interface
     * @since v1.0.0
     */
    abstract public function getFields($entity);

    /**
     * Validates that the value is of the proper type
     * @param mixed $value value to DataType
     * @param string $field field name
     * @return boolean
     * @since v1.0.0
     */
    abstract public function ValidateValue($value, $field = null);

    /**
     * method is called when a given event is triggered
     * @param Event $event Event object passed at time of triggering
     * @throws InvalidDataTypeException thrown when value does not DataType
     * @return void
     */
    public function onExecute(Event $event)
    {
        if (!$event->getTarget() instanceof Model) {
            return;
        }
        /* @var $model Model */
        $model = $event->getTarget();
        $entity = $model->getEntity();
        if (!is_a($entity, $this->getTypeInterface())) {
            return;
        }
        $this->DataType($model->getData(), $this->getFields($entity));
    }

    /**
     * checks data against list of fields for valid data types
     * @param array $data associative array of data to be DataTyped
     * @param array $fields fields that are deemed a given type
     * @throws InvalidDataTypeException
     */
    public function DataType(array $data, array $fields)
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $data) && !$this->ValidateValue($data[$field], $field)) {
                throw new InvalidDataTypeException($field);
            }
        }
    }
}
