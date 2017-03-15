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

use DSchoenbauer\Orm\Entity\HasDateFieldsInterface;

/**
 * Description of ValidateDate
 *
 * @author David Schoenbauer
 */
class ValidateDate extends AbstractValidate
{

    private $defaultDateTimeFormat;

    /**
     * returns date fields found in the entity
     * @param HasDateFieldsInterface $entity
     */
    public function getFields($entity)
    {
        $this->setDefaultDateTimeFormat($entity->getDateDefaultFormat());
        return $entity->getDateFields();
    }

    /**
     * returns an interface path that is the entity is checked for
     * @return string;
     */
    public function getTypeInterface()
    {
        return HasDateFieldsInterface::class;
    }

    /**
     * ensures that a given value is of a given type.
     * @param bool $value true value is valid, false the value is not
     */
    public function validateValue($value)
    {
        if ($value instanceof \DateTime) {
            return true;
        }

        if (is_object($value)) {
            return false;
        }

        return \DateTime::createFromFormat($this->getDefaultDateTimeFormat(), $value) instanceof \DateTime;
    }

    /**
     * returns a default Date Time String
     * @return string
     */
    protected function getDefaultDateTimeFormat()
    {
        return $this->defaultDateTimeFormat;
    }

    /**
     * 
     * @param string $defaultDateTimeFormat a format used to translate to a date
     * @return $this
     */
    protected function setDefaultDateTimeFormat($defaultDateTimeFormat)
    {
        $this->defaultDateTimeFormat = $defaultDateTimeFormat;
        return $this;
    }
}
