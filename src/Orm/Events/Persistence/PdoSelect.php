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
namespace DSchoenbauer\Orm\Events\Persistence;

use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Select;
use DSchoenbauer\Sql\Where\ArrayWhere;
use PDO;
use Zend\EventManager\EventInterface;

/**
 * Event driven hook to select information from a PDO connection
 *
 * @author David Schoenbauer
 */
class PdoSelect extends AbstractEvent
{

    private $adapter;
    private $select;

    /**
     * @param array $events An array of event names to bind to
     * @param PDO $adapter
     * @param Select $select
     */
    public function __construct(
        array $events,
        \PDO $adapter,
        $priority = EventPriorities::ON_TIME,
        Select $select = null
    ) {
    
        parent::__construct($events, $priority);
        $this->setAdapter($adapter)
            ->setSelect($select);
    }

    /**
     * event action
     * @param EventInterface $event object passed when event is fired
     * @return void
     * @since v1.0.0
     */
    public function onExecute(EventInterface $event)
    {
        if (!$event->getTarget() instanceof ModelInterface) {
            return; //Nothing to do with this event
        }
        /* @var $model ModelInterface */
        $model = $event->getTarget();
        $entity = $model->getEntity();
        $model->setData(
            $this->getSelect()
                ->setTable($entity->getTable())
                ->setFields($entity->getAllFields())
                ->setWhere(new ArrayWhere([$entity->getIdField() => $model->getId()]))->setFetchFlat()
                ->execute($this->getAdapter())
        );
    }

    /**
     * Returns a PHP Data Object
     * @return PDO
     * @since v1.0.0
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * PDO connection to a db of some sort.
     * @param PDO $adapter
     * @return $this
     * @since v1.0.0
     */
    public function setAdapter(PDO $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * object with logic for the Select. If Select is not provided one will be lazy loaded
     * @return Select
     * @since v1.0.0
     */
    public function getSelect()
    {
        if (!$this->select instanceof Select) {
            $this->select = new Select("");
        }
        return $this->select;
    }

    /**
     * Object that contains the select logic
     * @param Select $select
     * @return $this
     * @since v1.0.0
     */
    public function setSelect(Select $select = null)
    {
        $this->select = $select;
        return $this;
    }
}
