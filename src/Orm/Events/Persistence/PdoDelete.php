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
use Zend\EventManager\Event;

/**
 * Description of PdoDelete
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class PdoDelete extends AbstractEvent
{

    public $adapter;
    public $delete;

    public function __construct(PDO $adapter, Delete $delete = null)
    {
        $this->setAdapter($adapter)->setDelete($delete);
    }

    public function onExecute(Event $event)
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
     * @return Delete
     */
    public function getDelete()
    {
        if (!$this->delete) {
            $this->setDelete(new Delete(null));
        }
        return $this->delete;
    }

    public function setDelete($delete)
    {
        $this->delete = $delete;
        return $this;
    }
}
