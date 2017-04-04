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
namespace DSchoenbauer\Orm;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Framework\AttributeCollection;

/**
 *
 * @author David Schoenbauer
 */
interface ModelInterface
{

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager();

    /**
     * @return EntityInterface Description
     */
    public function getEntity();

    /**
     * sets the entity of the model
     * @param EntityInterface $entity
     * @return ModelInterface
     * @since v1.0.0
     */
    public function setEntity(EntityInterface $entity);

    /**
     * Allows the visitor pattern to expand the functionality of the object
     * @param VisitorInterface $visitor
     * @return ModelInterface
     * @since v1.0.0
     */
    public function accept(VisitorInterface $visitor);

    /**
     * provides a unique identifier value for a given record
     * @return integer
     * @since v1.0.0
     */
    public function getId();

    /**
     * provides the current record
     * @return mixed
     * @since v1.0.0
     */
    public function getData();

    /**
     * sets a unique identifier value for a given record
     * @param integer $id
     * @return ModelInterface
     * @since v1.0.0
     */
    public function setId($id);

    /**
     * sets the current record
     * @param mixed $data current
     * @return ModelInterface
     * @since v1.0.0
     */
    public function setData($data);

    /**
     * provides a collection of key value pairs that are specific to a given model
     * @return AttributeCollection
     * @since v1.0.0
     */
    public function getAttributes();

    /**
     * sets a collection of key value pairs that are specific to a given model
     * @param AttributeCollection $attributes
     * @return ModelInterface
     * @since v1.0.0
     */
    public function setAttributes(AttributeCollection $attributes);
}
