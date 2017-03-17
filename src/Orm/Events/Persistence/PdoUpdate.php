<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence;

use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Model;
use DSchoenbauer\Sql\Command\Update;
use DSchoenbauer\Sql\Where\ArrayWhere;
use PDO;
use Zend\EventManager\Event;

/**
 * Event driven hook to update information from a PDO connection
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class PdoUpdate extends AbstractEvent
{

    private $adapter;
    private $update;

    public function __construct(PDO $adapter, Update $update = null)
    {
        $this->setAdapter($adapter)->setUpdate($update);
    }

    /**
     * event action
     * @param Event $event object passed when event is fired
     * @return void
     * @since v1.0.0
     */
    public function onExecute(Event $event)
    {
        if (!$event->getTarget() instanceof Model) {
            return;
        }
        /* @var $model Model */
        $model = $event->getTarget();
        $entity = $model->getEntity();
        $this->getUpdate()
            ->setTable($entity->getTable())->setData($model->getData())
            ->setWhere(new ArrayWhere([$entity->getIdField() => $model->getId()]))
            ->execute($this->getAdapter());
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
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * object with logic for the Update. If Update is not provided one will be lazy loaded
     * @return Update
     * @since v1.0.0
     */
    public function getUpdate()
    {
        if (!$this->update instanceof Update) {
            $this->setUpdate(new Update(null, []));
        }
        return $this->update;
    }

    /**
     * Object that contains the update logic
     * @param Update $update
     * @return $this
     * @since v1.0.0
     */
    public function setUpdate(Update $update = null)
    {
        $this->update = $update;
        return $this;
    }
}
