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

use DateTime;
use DSchoenbauer\Orm\Entity\HasDateFieldsInterface;
use DSchoenbauer\Orm\Entity\HasDateWithCustomFormatInterface;

/**
 * Validates date fields are dates
 *
 * @author David Schoenbauer
 */
class DataTypeDate extends AbstractDataType
{

    private $defaultFormat;
    private $customDateTimeFormats = [];

    /**
     * returns the fields affected by the entity interface
     * @param mixed $entity an entity object implements the getTypeInterface
     * @return array an array of fields that are relevant to the interface
     * @since v1.0.0
     */
    public function getFields($entity)
    {
        $this->setDefaultDateTimeFormat($entity->getDateDefaultFormat());
        if ($entity instanceof HasDateWithCustomFormatInterface) {
            $this->customDateTimeFormats = $entity->getDateCustomFormat();
        }
        return $entity->getDateFields();
    }

    /**
     * full name space of an interface that defines a given field type
     * @return string
     * @since v1.0.0
     */
    public function getInterface()
    {
        return HasDateFieldsInterface::class;
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
        if ($value instanceof DateTime) {
            return true;
        }

        if (is_object($value)) {
            return false;
        }
        $format = array_key_exists($field, $this->customDateTimeFormats) ?
            $this->customDateTimeFormats[$field] : $this->getDefaultDateTimeFormat();
        return DateTime::createFromFormat($format, $value) instanceof DateTime;
    }

    /**
     * provides a default Date Time String
     * @return string
     * @since v1.0.0
     */
    public function getDefaultDateTimeFormat()
    {
        return $this->defaultFormat;
    }

    /**
     *
     * @param string $defaultFormat a format used to translate to a date
     * @return $this
     * @since v1.0.0
     */
    public function setDefaultDateTimeFormat($defaultFormat)
    {
        $this->defaultFormat = $defaultFormat;
        return $this;
    }
}
