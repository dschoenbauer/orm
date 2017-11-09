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

use DSchoenbauer\Orm\Entity\HasUniqueFieldsInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Exception\NonUniqueValueException;
use DSchoenbauer\Orm\ModelInterface;
use PDO;
use Zend\EventManager\EventInterface;

/**
 * Description of UniqueFields
 *
 * @author David Schoenbauer
 */
class UniqueFields extends AbstractEvent
{

    private $adapter;

    public function __construct(array $events = array(), $priority = EventPriorities::ON_TIME)
    {
        parent::__construct($events, $priority);
    }

    public function onExecute(EventInterface $event)
    {
        /* @var $model ModelInterface */
        $model = $event->getTarget();
        if (!$this->validateModel($model, HasUniqueFieldsInterface::class)) {
            return false;
        }
        /* @var $entity HasUniqueFieldsInterface */
        $entity = $model->getEntity();
        $data = $model->getData();

        $this->checkForDuplicates($data, $entity);

        return true;
    }

    public function checkForDuplicates(array $data, HasUniqueFieldsInterface $entity)
    {
        $fields = $entity->getUniqueFields();
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || !array_key_exists($entity->getIdField(), $data)) {
                continue; /* field doesn't exsit in data */
            }
            $sql = sprintf('SELECT count(1)>0 has_duplicate from %s 
                where %s != :id and %s = :value', $entity->getTable(), $entity->getIdField(), $field);
            $stmt = $this->getAdapter()->prepare($sql);
            $params = [$entity->getIdField() => $data[$entity->getIdField()], $field => $data[$field]];
            $stmt->execute($params);
            if ($stmt->fetchColumn()) {
                throw new NonUniqueValueException();
            }
        }
    }

    /**
     * @return PDO
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter(PDO $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }
}
