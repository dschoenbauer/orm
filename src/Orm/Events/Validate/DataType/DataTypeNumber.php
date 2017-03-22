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
namespace DSchoenbauer\Orm\Events\Validate\DataType;

use DSchoenbauer\Orm\Entity\HasNumericFieldsInterface;

/**
 * Validates number fields are numbers
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class DataTypeNumber extends AbstractDataType
{

    /**
     * returns the fields affected by the entity interface
     * @param mixed $entity an entity object implements the getTypeInterface
     * @return array an array of fields that are relevant to the interface
     * @since v1.0.0
     */
    public function getFields($entity)
    {
        return $entity->getNumericFields();
    }

    /**
     * full name space of an interface that defines a given field type
     * @return string
     * @since v1.0.0
     */
    public function getTypeInterface()
    {
        return HasNumericFieldsInterface::class;
    }

    /**
     * Validates that the value is of the proper type
     * @param mixed $value value to DataType
     * @param string $field field name
     * @return boolean
     * @since v1.0.0
     */
    public function validateValue($value, $field = null)
    {
        return is_numeric($value);
    }
}
