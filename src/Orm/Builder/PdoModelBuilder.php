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
namespace DSchoenbauer\Orm\Builder;

use DSchoenbauer\Orm\Builder\Component\PdoPersistence;
use DSchoenbauer\Orm\CrudModel;
use DSchoenbauer\Orm\Entity\EntityInterface;
use PDO;

/**
 * Builds a standard model
 *
 * @author David Schoenbauer
 */
class PdoModelBuilder extends AbstractBuilder
{

    /**
     * @var PDO
     */
    protected $adapter;

    public function __construct(\PDO $adapter, EntityInterface $entity)
    {
        parent::__construct($entity);
        $this->setAdapter($adapter)->setModel(new CrudModel($entity));
    }

    public function addPersistence()
    {
        $adapter = $this->getAdapter();
        $this->getModel()->accept(new PdoPersistence($adapter));
    }

    /**
     * @return PDO
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param PDO $adapter
     * @return $this
     */
    public function setAdapter(PDO $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }
}
