<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence;

use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Model;
use DSchoenbauer\Sql\Command\Create;
use DSchoenbauer\Sql\Where\ArrayWhere;
use PDO;
use Zend\EventManager\Event;

/**
 * Description of PdoCreate
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class PdoCreate extends AbstractEvent
{

    private $adapter;
    private $create;

    public function __construct(PDO $adapter, Create $create= null)
    {
        $this->setAdapter($adapter)->setCreate($create);
    }

    public function onExecute(Event $event)
    {
        if (!$event->getTarget() instanceof Model) {
            return;
        }
        /* @var $model Model */
        $model = $event->getTarget();
        $entity = $model->getEntity();
        $this->getCreate()
            ->setTable($entity->getTable())
            ->setData($model->getData())
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
     * @return Create
     */
    public function getCreate()
    {
        if (!$this->create instanceof Create) {
            $this->setCreate(new Create(null, []));
        }
        return $this->create;
    }

    public function setCreate($create)
    {
        $this->create = $create;
        return $this;
    }
}
