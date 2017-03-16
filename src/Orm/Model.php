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

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Framework\AttributeCollection;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * An object that represents business logic and is available for general consumption
 *
 * @author David Schoenbauer
 */
class Model
{

    private $id;
    private $data;
    private $attributes;
    private $entity;

    use EventManagerAwareTrait;

    /**
     * @param EntityInterface $entity an entity object houses specific 
     * information about a given model
     */
    public function __construct(EntityInterface $entity)
    {
        $this->setAttributes(new AttributeCollection())->setEntity($entity);
    }

    /**
     * provides the entity of the model
     * @return EntityInterface
     * @since v1.0.0
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * sets the entity of the model
     * @param EntityInterface $entity
     * @return Model
     * @since v1.0.0
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Allows the visitor pattern to expand the functionality of the object
     * @param \DSchoenbauer\Orm\VisitorInterface $visitor
     * @return Model
     * @since v1.0.0
     */
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visitModel($this);
        return $this;
    }

    /**
     * provides a unique identifier value for a given record
     * @return integer
     * @since v1.0.0
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * provides the current record
     * @return mixed
     * @since v1.0.0
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * sets a unique identifier value for a given record
     * @param integer $id
     * @return Model
     * @since v1.0.0
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * sets the current record 
     * @param mixed $data current 
     * @return Model
     * @since v1.0.0
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }


    /**
     * provides a collection of key value pairs that are specific to a given model
     * @return AttributeCollection
     * @since v1.0.0
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * sets a collection of key value pairs that are specific to a given model
     * @param AttributeCollection $attributes
     * @return Model
     * @since v1.0.0
     */
    public function setAttributes(AttributeCollection $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
}
