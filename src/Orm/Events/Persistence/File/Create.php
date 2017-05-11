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
namespace DSchoenbauer\Orm\Events\Persistence\File;

use DSchoenbauer\Orm\ModelInterface;

/**
 * Description of Create
 *
 * @author David Schoenbauer
 */
class Create extends AbstractFileEvent
{

    public function processAction(ModelInterface $model, array $existingData)
    {
        $data = $this->addData($existingData, $model);
        return $this->saveFile($data, $model->getEntity()) !== false;
    }

    public function addData(array $existingData, ModelInterface $model)
    {
        //Add the new record to the existing.
        $existingData[] = $model->getData();
        //Get the Id of the new record
        $idx = $this->getId($existingData);
        //Set the Id of the new record
        if (is_array($existingData[$idx])) {
            $existingData[$idx][$model->getEntity()->getIdField()] = $idx;
        }
        $model->setId($idx);
        $model->setData($existingData[$idx]);
        //return the new record with the id
        return $existingData;
    }

    public function getId(array $array)
    {
        end($array);
        return key($array);
    }
}
