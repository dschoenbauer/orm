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
 * Description of Model
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

    public function __construct(EntityInterface $entity)
    {
        $this->setAttributes(new AttributeCollection())->setEntity($entity);
    }
    
    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
        return $this;
    }
    
    
    function accept(VisitorInterface $visitor)
    {
        $visitor->visitModel($this);
        return $this;
    }

    function getId()
    {
        return $this->id;
    }

    function getData()
    {
        return $this->data;
    }

    function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * @return AttributeCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(AttributeCollection $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }
}
