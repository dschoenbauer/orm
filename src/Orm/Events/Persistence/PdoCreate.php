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
use PDO;
use Zend\EventManager\EventInterface;

/**
* Event driven hook to creates information from a PDO connection
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class PdoCreate extends AbstractEvent
{

    private $adapter;
    private $create;

    /**
     *
     * @param array $events An array of event names to bind to
     * @param PDO $adapter PDO connection to a db of some sort.
     * @param Create $create default null, if no create object offered one will
     * be lazy loaded for you
     * @since v1.0.0
     */
    public function __construct(array $events, PDO $adapter, Create $create = null)
    {
        parent::__construct($events);
        $this->setAdapter($adapter)->setCreate($create);
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
        $this->getCreate()
            ->setTable($entity->getTable())
            ->setData($model->getData())
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
     * object with logic for the Create. If Create is not provided one will be lazy loaded
     * @return Create object that is used for the create logic
     * @since v1.0.0
     */
    public function getCreate()
    {
        if (!$this->create instanceof Create) {
            $this->setCreate(new Create(null, []));
        }
        return $this->create;
    }

    /**
     * @param Create $create object with the create logic
     * @return $this
     * @since v1.0.0
     */
    public function setCreate(Create $create = null)
    {
        $this->create = $create;
        return $this;
    }
}
