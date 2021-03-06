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
     * @param ModelInterface $event object passed when event is fired
     * @return bool
     * @since v1.0.0
     */
    public function commit(ModelInterface $model)
    {
        try {
            /* @var $model ModelInterface */
            $entity = $model->getEntity();
            $idx = $this->getCreate()
                ->setIsStrict()
                ->setTable($entity->getTable())
                ->setData($this->reduceFields($model->getData(), $entity->getAllFields()))
                ->execute($this->getAdapter());
            $model->setId($idx);
        } catch (NoRecordsAffectedException $exc) {
            throw new RecordNotFoundException();
        }
        return true;
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
