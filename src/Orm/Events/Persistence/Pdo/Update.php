<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Orm\Events\Persistence\Pdo;

use DSchoenbauer\Orm\Exception\RecordNotFoundException;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Update as UpdateCommand;
use DSchoenbauer\Sql\Exception\NoRecordsAffectedException;
use DSchoenbauer\Sql\Where\ArrayWhere;

/**
 * Event driven hook to update information from a PDO connection
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Update extends AbstractPdoEvent
{

    private $update;

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
            $this->getUpdate()
                ->setIsStrict()
                ->setTable($entity->getTable())
                ->setData($this->reduceFields($model->getData(), $entity->getAllFields()))
                ->setWhere(new ArrayWhere([$entity->getIdField() => $model->getId()]))
                ->execute($this->getAdapter());
            return true;
        } catch (NoRecordsAffectedException $exc) {
            throw new RecordNotFoundException();
        }
    }

    /**
     * object with logic for the Update. If Update is not provided one will be lazy loaded
     * @return UpdateCommand
     * @since v1.0.0
     */
    public function getUpdate()
    {
        if (!$this->update instanceof UpdateCommand) {
            $this->setUpdate(new UpdateCommand(null, []));
        }
        return $this->update;
    }

    /**
     * Object that contains the update logic
     * @param UpdateCommand $update
     * @return $this
     * @since v1.0.0
     */
    public function setUpdate(UpdateCommand $update = null)
    {
        $this->update = $update;
        return $this;
    }
}
