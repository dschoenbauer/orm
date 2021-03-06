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

use ArrayAccess;
use DSchoenbauer\Orm\Events\AbstractModelEvent;
use DSchoenbauer\Orm\ModelInterface;

/**
 * Framework for validating data types
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
abstract class AbstractValidate extends AbstractModelEvent
{

    protected $model;
    protected $params;

    /**
     * returns the fields affected by the entity interface
     * @param mixed $entity an entity object implements the getTypeInterface
     * @return array an array of fields that are relevant to the interface
     * @since v1.0.0
     */
    abstract public function getFields($entity);

    /**
     *
     * @param ModelInterface $model
     * @since v1.5.0
     */
    public function execute(ModelInterface $model)
    {
        $this->setModel($model);

        if (!$this->preExecuteCheck()) {
            return false;
        }
        return $this->validate($model->getData(), $this->getFields($model->getEntity()));
    }

    /**
     * used to check if they validate function is required or relevant
     * @return boolean
     */
    public function preExecuteCheck()
    {
        return true;
    }

    /**
     * validates data against list of fields
     * @param array $data associative array of data to be validated
     * @param array $fields fields that are deemed a given type
     * @return boolean
     * @since v1.0.0
     */
    abstract public function validate(array $data, array $fields);

    /**
     * provides model for which data type exists
     * @return ModelInterface
     * @since v1.0.0
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * set model for which data type exists
     * @param ModelInterface $model
     * @return AbstractValidate
     * @since v1.0.0
     */
    public function setModel(ModelInterface $model)
    {
        $this->model = $model;
        return $this;
    }
}
