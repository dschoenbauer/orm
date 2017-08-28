<?php
/*
 * The MIT License
 *
 * Copyright 2017 David Schoenbauer.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace DSchoenbauer\Orm\Events\Validate\Schema;

use DSchoenbauer\Orm\Entity\HasVersionControlIndexInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Exception\InvalidDataTypeException;
use DSchoenbauer\Orm\Exception\RecordOutOfDateException;
use DSchoenbauer\Orm\Exception\RequiredFieldMissingException;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Select;
use DSchoenbauer\Sql\Where\ArrayWhere;
use PDO;
use Zend\EventManager\EventInterface;

/**
 * Validates that data for update is the current record
 *
 * @author David Schoenbauer
 */
class VersionControlIndex extends AbstractEvent
{

    private $adapter;
    private $select;

    public function __construct(PDO $adapter, array $events = array(), $priority = EventPriorities::ON_TIME)
    {
        $this->setAdapter($adapter);
        parent::__construct($events, $priority);
    }

    public function onExecute(EventInterface $event)
    {
        /* @var $model ModelInterface */
        $model = $event->getTarget();
        if (!$this->validateModel($model, HasVersionControlIndexInterface::class)) {
            return false;
        }
        $field = $model->getEntity()->getVersionControlField();
        $data = $model->getData();
        $this->validateFieldExists($data, $field);
        $this->validateFieldType($data[$field]);
        $this->validateValueIsCurrent($model, $field, $data[$field]);
        $data[$field] = $this->increment($data[$field]);
        $model->setData($data);
        return true;
    }

    public function validateFieldExists(array $data, $field)
    {
        if (!array_key_exists($field, $data)) {
            throw new RequiredFieldMissingException([$field]);
        }
        return true;
    }

    public function validateFieldType($value)
    {
        if (!is_numeric($value)) {
            throw new InvalidDataTypeException();
        }
        return true;
    }

    public function validateValueIsCurrent(ModelInterface $model, $field, $userValue)
    {
        $systemValue = $this
            ->getSelect()
            ->setFetchFlat()
            ->setFetchStyle(\PDO::FETCH_ASSOC | \PDO::FETCH_COLUMN)
            ->setFields([$field])
            ->setTable($model->getEntity()->getTable())
            ->setWhere(new ArrayWhere([$model->getEntity()->getIdField() => $model->getId()]))
            ->execute($this->getAdapter());
        if ($systemValue !== $userValue) {
            throw new RecordOutOfDateException();
        }
        return true;
    }

    public function increment($value)
    {
        return ++$value;
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
     *
     * @return Select
     */
    public function getSelect()
    {
        if (!$this->select) {
            $this->setSelect(new Select(''));
        }
        return $this->select;
    }

    public function setSelect($select)
    {
        $this->select = $select;
        return $this;
    }
}
