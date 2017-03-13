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
 * Description of PdoUpdate
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

    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return Update
     */
    public function getUpdate()
    {
        if (!$this->update instanceof Update) {
            $this->setUpdate(new Update(null, []));
        }
        return $this->update;
    }

    public function setUpdate($update)
    {
        $this->update = $update;
        return $this;
    }
}
