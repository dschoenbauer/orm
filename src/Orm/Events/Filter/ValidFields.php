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

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Events\Validate\AbstractValidate;

/**
 * Validates that only fields valid fields are allowed in the data model
 *
 * @author David Schoenbauer
 */
class ValidFields extends AbstractValidate
{

    /**
     * provides a list of fields that this operation will affect
     * @param EntityInterface $entity entity object that houses the fields
     * @return array
     * @since v1.0.0
     */
    public function getFields($entity)
    {
        /* @var $entity EntityInterface */
        return $entity->getAllFields();
    }

    /**
     * Interface that qualifies the entity as valid for this operation
     * @return string
     * @since v1.0.0
     */
    public function getTypeInterface()
    {
        return EntityInterface::class;
    }

    /**
     * removes any outside fields and saves that data to the model
     * @param array $data
     * @param array $fields
     * @return boolean
     * @since v1.0.0
     */
    public function validate(array $data, array $fields)
    {
        $filledFields = array_fill_keys($fields, null);
        $filteredData = array_intersect_key($data, $filledFields);
        $this->getModel()->setData($filteredData);
        return true;
    }
}
