<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Exception\RecordNotFoundException;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Create as CreateCommand;
use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use Zend\EventManager\EventInterface;

/**
 * Event driven hook to creates information from a PDO connection
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Create extends AbstractPdoEvent
{

    private $create;

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
        try {
            /* @var $model ModelInterface */
            $model = $event->getTarget();
            $entity = $model->getEntity();
            $this->getCreate()
                ->setIsStrict()
                ->setTable($entity->getTable())
                ->setData($model->getData())
                ->execute($this->getAdapter());
        } catch (NoRecordsAffectedException $exc) {
            throw new RecordNotFoundException();
        }
    }

    /**
     * object with logic for the Create. If Create is not provided one will be lazy loaded
     * @return CreateCommand object that is used for the create logic
     * @since v1.0.0
     */
    public function getCreate()
    {
        if (!$this->create instanceof CreateCommand) {
            $this->setCreate(new CreateCommand(null, []));
        }
        return $this->create;
    }

    /**
     * @param CreateCommand $create object with the create logic
     * @return $this
     * @since v1.0.0
     */
    public function setCreate(CreateCommand $create = null)
    {
        $this->create = $create;
        return $this;
    }
}
