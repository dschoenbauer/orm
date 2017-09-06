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
namespace DSchoenbauer\Tests\Orm\Events\Persistence\Http;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Entity\HasUriCollection;
use DSchoenbauer\Orm\Entity\HasUriEntity;
use DSchoenbauer\Orm\ModelInterface;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Description of TestModelTrait
 *
 * @author David Schoenbauer
 */
trait TestModelTrait
{

    /**
     * 
     * @param type $idValue
     * @param type $data
     * @param type $entity
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getModel($idValue = 0, $data = [], $entity = null)
    {
        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->any())->method('getId')->willReturn($idValue);
        $model->expects($this->any())->method('setData')->willReturnCallback(function($data) use ($model) {
            $this->_data = $data;
            return $model;
        });
        $model->expects($this->any())->method('getData')->willReturnCallback(function() {
            return $this->_data;
        });
        $model->expects($this->any())->method('getEntity')->willReturn($entity);
        $model->setData($data);
        return $model;
    }

    public function getAbstractEntity($idField = null, $tableName = null)
    {
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->any())->method('getIdField')->willReturn($idField);
        $entity->expects($this->any())->method('getTable')->willReturn($tableName);
        return $entity;
    }

    public function getIsHttp($idField = null, $entityUrl = null, $collectionUrl = null, $useEntity = false)
    {

        $entity = $this->getMockBuilder(($useEntity ? HasUriEntity::class : HasUriCollection::class))->getMock();
        $entity->expects($this->any())->method('getIdField')->willReturn($idField);
        if ($useEntity) {
            $entity->expects($this->any())->method('getUriEntityMask')->willReturn($entityUrl);
        } else {
            $entity->expects($this->any())->method('getUriCollectionMask')->willReturn($collectionUrl);
        }
        return $entity;
    }
}
