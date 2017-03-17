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
namespace DSchoenbauer\Orm\Events\Validate;

use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Exception\InvalidDataTypeException;
use DSchoenbauer\Orm\Model;
use Zend\EventManager\Event;

/**
 * Framework for validating data types
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
abstract class AbstractValidate extends AbstractEvent
{

    protected $model;
    protected $params;

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
     * method is called when a given event is triggered
     * @param Event $event Event object passed at time of triggering
     * @throws InvalidDataTypeException thrown when value does not validate
     * @return void
     * @since v1.0.0
     */
    public function onExecute(Event $event)
    {
        if (!$event->getTarget() instanceof Model) {
            return;
        }
        $this->setModel($event->getTarget());
        $this->setParams($event->getParams());

        $entity = $this->getModel()->getEntity();
        if (!is_a($entity, $this->getTypeInterface())) {
            return;
        }
        $this->validate($this->getModel()->getData(), $this->getFields($entity));
    }

    /**
     * validates data against list of fields 
     * @param array $data associative array of data to be validated
     * @param array $fields fields that are deemed a given type
     * @since v1.0.0
     */
    abstract public function validate(array $data, array $fields);

    /**
     * provides model for which data type exists
     * @return Model
     * @since v1.0.0
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * set model for which data type exists
     * @param Model $model
     * @return AbstractValidate
     * @since v1.0.0
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Get all parameters
     * @return array|object|ArrayAccess
     * @since v1.0.0
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set parameters
     *
     * @param  array|ArrayAccess|object $params
     * @since v1.0.0
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }
}
