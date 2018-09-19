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
namespace DSchoenbauer\Orm\Events\Filter;

use DSchoenbauer\Exception\Http\ClientError\UnauthorizedException;
use DSchoenbauer\Orm\Entity\HasPasswordInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Enum\ModelEvents;
use DSchoenbauer\Orm\Events\AbstractModelEvent;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Sql\Command\Select;
use DSchoenbauer\Sql\Where\ArrayWhere;
use PDO;
use stdClass;

/**
 * Description of PasswordValidate
 *
 * @author David Schoenbauer
 */
class PasswordValidate extends AbstractModelEvent
{

    private $adapter;
    private $select;

    public function __construct(array $events, PDO $adapter, $priority = EventPriorities::ON_TIME)
    {
        $this->setAdapter($adapter);
        parent::__construct($events, $priority);
    }

    public function getInterface()
    {
        return HasPasswordInterface::class;
    }

    public function execute(ModelInterface $model)
    {
        /* @var $entity HasPasswordInterface */
        $entity = $model->getEntity();
        if (!$idx = $this->validateUser($model->getData(), $entity)) {
            throw new UnauthorizedException();
        }
        $model->setId($idx);
        $model->getEventManager()->trigger(ModelEvents::AUTHENTICATION_SUCCESS, $model);
        return true;
    }

    /**
     * @param mixed $data
     * @param HasPasswordInterface $passwordInfo
     * @return boolean
     */
    public function validateUser($data, HasPasswordInterface $passwordInfo)
    {
        if (!array_key_exists($passwordInfo->getPasswordField(), $data ?: []) ||
            !array_key_exists($passwordInfo->getUserNameField(), $data ?: [])
        ) {
            return false;
        }
        $meta = $this->getPasswordMetaData($data[$passwordInfo->getUserNameField()], $passwordInfo);
        return $passwordInfo->getPasswordMaskStrategy()->validate($data[$passwordInfo->getPasswordField()], $meta->hash) ? $meta->id : false;
    }

    public function getPasswordMetaData($userName, HasPasswordInterface $passwordInfo)
    {
        return $this->getSelect()
                ->setTable($passwordInfo->getTable())
                ->setFields([$passwordInfo->getPasswordField()])
                ->setWhere(new ArrayWhere([$passwordInfo->getUserNameField() => $userName]))
                ->setFetchFlat()
                ->setFetchStyle(\PDO::FETCH_OBJ)
                ->setDefaultValue($this->getNullReturn())
                ->execute($this->getAdapter());
    }
    
    public function getNullReturn($id = null, $hash = null)
    {
        $obj = new stdClass();
        $obj->id = $id;
        $obj->hash = $hash;
        return $obj;
    }

    /**
     * @return Select
     */
    public function getSelect()
    {
        if (!$this->select) {
            $this->setSelect(new Select(null));
        }
        return $this->select;
    }

    public function setSelect(Select $select)
    {
        $this->select = $select;
        return $this;
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
