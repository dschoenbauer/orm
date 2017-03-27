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
namespace DSchoenbauer\Orm\Builder;

use DSchoenbauer\Orm\CrudModel;
use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Enum\ModelEvents;
use DSchoenbauer\Orm\Events\Persistence\PdoCreate;
use DSchoenbauer\Orm\Events\Persistence\PdoDelete;
use DSchoenbauer\Orm\Events\Persistence\PdoSelect;
use DSchoenbauer\Orm\Events\Persistence\PdoUpdate;
use DSchoenbauer\Orm\Events\Validate\DataType\DataTypeBoolean;
use DSchoenbauer\Orm\Events\Validate\DataType\DataTypeDate;
use DSchoenbauer\Orm\Events\Validate\DataType\DataTypeNumber;
use DSchoenbauer\Orm\Events\Validate\DataType\DataTypeString;
use DSchoenbauer\Orm\Events\Validate\Schema\AliasEntityCollection;
use DSchoenbauer\Orm\Events\Validate\Schema\AliasEntitySingle;
use DSchoenbauer\Orm\Events\Validate\Schema\DefaultValue;
use DSchoenbauer\Orm\Events\Validate\Schema\RemoveId;
use DSchoenbauer\Orm\Events\Validate\Schema\RequiredFields;
use DSchoenbauer\Orm\Events\Validate\Schema\ValidFields;
use PDO;

/**
 * Builds a standard model
 *
 * @author David Schoenbauer
 */
class PdoModelBuilder implements BuilderInterface
{

    /**
     * @var PDO
     */
    protected $adapter;

    /**
     * @var CrudModel
     */
    protected $model;

    public function __construct(\PDO $adapter, EntityInterface $entity)
    {
        $this->setAdapter($adapter)->setModel(new CrudModel($entity));
    }

    public function build()
    {
        return $this->getModel();
    }

    public function buildFinalOutput()
    {
        $this->getModel()
            ->accept(new AliasEntityCollection([ModelEvents::FETCH_ALL], AliasEntityCollection::APPLY_ALIAS))
            ->accept(new AliasEntitySingle([ModelEvents::FETCH_ALL], AliasEntitySingle::APPLY_ALIAS));
    }

    public function buildPersistence()
    {
        $adapter = $this->getAdapter();
        $this->getModel()
            ->accept(new PdoCreate([ModelEvents::CREATE], $adapter))
            ->accept(new PdoSelect([ModelEvents::FETCH], $adapter))
            ->accept(new PdoUpdate([ModelEvents::UPDATE], $adapter))
            ->accept(new PdoDelete([ModelEvents::DELETE], $adapter));
    }

    public function buildValidations()
    {
        $this->getModel()
            ->accept(new AliasEntityCollection([ModelEvents::CREATE, ModelEvents::UPDATE], AliasEntityCollection::REMOVE_ALIAS))
            ->accept(new AliasEntitySingle([ModelEvents::CREATE, ModelEvents::UPDATE], AliasEntitySingle::REMOVE_ALIAS))
            ->accept(new RemoveId([ModelEvents::CREATE, ModelEvents::UPDATE]))
            ->accept(new ValidFields([ModelEvents::CREATE, ModelEvents::UPDATE]))
            ->accept(new DefaultValue([ModelEvents::CREATE]))
            ->accept(new RequiredFields([ModelEvents::CREATE, ModelEvents::UPDATE]))
            ->accept(new DataTypeBoolean([ModelEvents::CREATE, ModelEvents::UPDATE]))
            ->accept(new DataTypeDate([ModelEvents::CREATE, ModelEvents::UPDATE]))
            ->accept(new DataTypeNumber([ModelEvents::CREATE, ModelEvents::UPDATE]))
            ->accept(new DataTypeString([ModelEvents::CREATE, ModelEvents::UPDATE]))

        ;
    }

    /**
     * @return PDO
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param PDO $adapter
     * @return $this
     */
    public function setAdapter(PDO $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @return CrudModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param CrudModel $model
     * @return $this
     */
    public function setModel(CrudModel $model)
    {
        $this->model = $model;
        return $this;
    }
}
