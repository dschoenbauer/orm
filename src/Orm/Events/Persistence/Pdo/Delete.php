<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Delete as DeleteCommand;
use DSchoenbauer\Sql\Where\ArrayWhere;
use PDO;
use Zend\EventManager\EventInterface;

/**
 * Event driven hook to delete information from a PDO connection
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Delete extends AbstractPdoEvent
{

    private $delete;

    /**
     * event action
     * @param EventInterface $event object passed when event is fired
     * @return void
     * @since v1.0.0
     */
    public function onExecute(EventInterface $event)
    {
        if (!$event->getTarget() instanceof ModelInterface) {
            return;
        }
        /* @var $model ModelInterface */
        $model = $event->getTarget();
        $entity = $model->getEntity();
        $this->getDelete()
            ->setTable($entity->getTable())
            ->setWhere(new ArrayWhere([$entity->getIdField() => $model->getId()]))
            ->execute($this->getAdapter());
    }

    /**
     * object with logic for the delete. If Delete is not provided one will be lazy loaded
     * @return DeleteCommand
     * @since v1.0.0
     */
    public function getDelete()
    {
        if (!$this->delete) {
            $this->setDelete(new DeleteCommand(null));
        }
        return $this->delete;
    }

    /**
     * Object that contains the delete logic
     * @param Delete $delete
     * @return $this
     * @since v1.0.0
     */
    public function setDelete(DeleteCommand $delete = null)
    {
        $this->delete = $delete;
        return $this;
    }
}
