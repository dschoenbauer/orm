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
namespace DSchoenbauer\Orm\DataProvider;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Sql\Command\Select;
use PDO;

/**
 * Description of EntityDataProvider
 *
 * @author David Schoenbauer
 */
class EntityDataProvider implements DataProviderInterface
{

    private $entity;
    private $select;
    private $adapter;

    public function getData()
    {
        $this->getSelect()
            ->setTable($this->getEntity()->getTable())
            ->setFields(array_merge([$this->getEntity()->getIdField() . ' idx'], $this->getEntity()->getAllFields()))
            ->setFetchStyle(\PDO::FETCH_ASSOC | \PDO::FETCH_UNIQUE)
            ->setFetchFlat(false);
        return $this->getSelect()->execute($this->getAdapter());
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

    /**
     * @return Select
     */
    public function getSelect()
    {
        if (!$this->select) {
            $this->setSelect(new Select(''));
        }
        return $this->select;
    }

    public function setSelect(Select $select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @return PDO
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter(PDO $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }
}
