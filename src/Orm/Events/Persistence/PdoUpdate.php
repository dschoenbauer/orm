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
use Zend\EventManager\EventInterface;

/**
 * Event driven hook to update information from a PDO connection
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class PdoUpdate extends AbstractEvent
{

    private $adapter;
    private $update;

    /**
     * @param array $events An array of event names to bind to
     * @param PDO $adapter
     * @param Update $update
     */
    public function __construct(array $events, PDO $adapter, Update $update = null)
    {
        parent::__construct($events);
        $this->setAdapter($adapter)->setUpdate($update);
    }

    /**
     * event action
     * @param EventInterface $event object passed when event is fired
     * @return void
     * @since v1.0.0
     */
    public function onExecute(EventInterface $event)
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
