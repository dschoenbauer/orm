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
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Exception\RecordNotFoundException;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Select as SelectCommand;
use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use DSchoenbauer\Sql\Where\ArrayWhere;
use Zend\EventManager\EventInterface;

/**
 * Event driven hook to select information from a PDO connection
 *
 * @author David Schoenbauer
 */
class Select extends AbstractPdoEvent
{

    private $select;

    /**
     * event action
     * @param EventInterface $event object passed when event is fired
     * @return void
     * @since v1.0.0
     */
    public function commit(EventInterface $event)
    {
        try {
            /* @var $model ModelInterface */
            $model = $event->getTarget();
            $entity = $model->getEntity();
            $model->setData(
                $this->getSelect()
                    ->setIsStrict()
                    ->setTable($entity->getTable())
                    ->setFields($entity->getAllFields())
                    ->setWhere(new ArrayWhere([$entity->getIdField() => $model->getId()]))
                    ->setFetchFlat()
                    ->execute($this->getAdapter())
            );
        } catch (NoRecordsAffectedException $exc) {
            throw new RecordNotFoundException();
        }
    }

    /**
     * object with logic for the Select. If Select is not provided one will be lazy loaded
     * @return SelectCommand
     * @since v1.0.0
     */
    public function getSelect()
    {
        if (!$this->select instanceof SelectCommand) {
            $this->select = new SelectCommand("");
        }
        return $this->select;
    }

    /**
     * Object that contains the select logic
     * @param SelectCommand $select
     * @return $this
     * @since v1.0.0
     */
    public function setSelect(SelectCommand $select = null)
    {
        $this->select = $select;
        return $this;
    }
}
