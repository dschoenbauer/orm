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

use DSchoenbauer\Orm\Enum\ModelEvents;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Model;
use DSchoenbauer\Sql\Command\Select;
use DSchoenbauer\Sql\Where\ArrayWhere;
use PDO;
use Zend\EventManager\Event;

/**
 * Description of PdoSelect
 *
 * @author David Schoenbauer
 */
class PdoSelect extends AbstractEvent
{

    private $adapter;
    private $select;

    public function __construct(\PDO $adapter, Select $select = null)
    {
        $this->setEvents([ModelEvents::FETCH])
            ->setAdapter($adapter)
            ->setSelect($select);
    }

    public function onExecute(Event $event)
    {
        if (!$event->getTarget() instanceof Model) {
            return; //Nothing to do with this event
        }
        /* @var $model Model */
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

    /**
     *
     * @return Select
     */
    public function getSelect()
    {
        if (!$this->select instanceof Select) {
            $this->select = new Select("");
        }
        return $this->select;
    }

    public function setSelect(Select $select = null)
    {
        $this->select = $select;
        return $this;
    }
}