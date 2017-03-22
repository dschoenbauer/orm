<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence;

use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Model;
use DSchoenbauer\Sql\Command\Delete;
use DSchoenbauer\Sql\Where\ArrayWhere;
use PDO;
use Zend\EventManager\EventInterface ;

/**
 * Event driven hook to delete information from a PDO connection
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class PdoDelete extends AbstractEvent
{

    private $adapter;
    private $delete;

    /**
     * @param array $events An array of event names to bind to
     * @param PDO $adapter
     * @param Delete $delete
     */
    public function __construct(array $events, PDO $adapter, Delete $delete = null)
    {
        parent::__construct($events);
        $this->setAdapter($adapter)->setDelete($delete);
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
        $this->getDelete()
            ->setTable($entity->getTable())
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
    public function setAdapter(PDO $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * object with logic for the delete. If Delete is not provided one will be lazy loaded
     * @return Delete
     * @since v1.0.0
     */
    public function getDelete()
    {
        if (!$this->delete) {
            $this->setDelete(new Delete(null));
        }
        return $this->delete;
    }

    /**
     * Object that contains the delete logic
     * @param Delete $delete
     * @return $this
     * @since v1.0.0
     */
    public function setDelete(Delete $delete = null)
    {
        $this->delete = $delete;
        return $this;
    }
}
