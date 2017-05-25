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
namespace DSchoenbauer\Orm\Events\Filter\DataType;

use DateTime;
use DateTimeZone;
use DSchoenbauer\Orm\Entity\HasDateFieldsInterface;
use DSchoenbauer\Orm\Entity\HasDateWithCustomFormatInterface;
use DSchoenbauer\Orm\Enum\ModelAttributes;
use DSchoenbauer\Orm\Events\Filter\AbstractEventFilter;
use DSchoenbauer\Orm\Exception\InvalidDataTypeException;
use DSchoenbauer\Orm\ModelInterface;

/**
 * Converts date strings to date objects
 *
 * @author David Schoenbauer
 */
class DateFilter extends AbstractEventFilter
{

    public function filter(array $data)
    {
        $timeZone = $this->getModel()->getAttributes()->get(ModelAttributes::TIME_ZONE, new DateTimeZone('UTC'));
        $formats = $this->getDateFormats($this->getModel());
        return $this->formatDate($data, $formats, $timeZone);
    }

    public function formatDate(array $data, array $formats, DateTimeZone $timeZone)
    {
        foreach ($formats as $field => $format) {
            if (!array_key_exists($field, $data) || $data[$field] instanceof DateTime) {
                continue;
            }
            if (!$protoDateTime = DateTime::createFromFormat($format, $data[$field], $timeZone)) {
                throw new InvalidDataTypeException();
            }
            $data[$field] = $protoDateTime;
        }

        return $data;
    }

    public function getDateFormats(ModelInterface $model)
    {
        $dateFields = [];
        $dateFormat = null;
        if ($model->getEntity() instanceof HasDateFieldsInterface) {
            $dateFields = $model->getEntity()->getDateFields();
            $dateFormat = $model->getEntity()->getDateDefaultFormat();
        }
        $formats = array_fill_keys($dateFields, $dateFormat);

        if ($model->getEntity() instanceof HasDateWithCustomFormatInterface) {
            return array_merge($formats, $model->getEntity()->getDateCustomFormat());
        }
        return $formats;
    }
}
