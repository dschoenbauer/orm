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
namespace DSchoenbauer\Orm\Exception;

use DSchoenbauer\Exception\Http\ClientError\BadRequestException;
use DSchoenbauer\Orm\Enum\ExceptionDefaultMessages;

/**
 * A required field is not present in the data payload
 *
 * @author David Schoenbauer
 * @since v1.0.0
 */
class RequiredFieldMissingException extends BadRequestException implements OrmExceptionInterface
{

    protected $missingFields;

    public function __construct(array $missingFields = [], $message = null)
    {
        $this->setMissingFields($missingFields);
        if (!$message) {
            $message = $this->getDefaultMessage();
        }
        parent::__construct($this->interpolateMessage($message, $this->getMissingFields()));
    }

    public function interpolateMessage($message, $fields, $noFieldsIdentifiedMessage = 'no fields identified')
    {
        $fieldString = implode(', ', $fields ?: [$noFieldsIdentifiedMessage]);
        return sprintf($message, $fieldString);
    }

    /**
     * Provides a list of fields that are missing
     * @return array
     */
    public function getMissingFields()
    {
        return $this->missingFields;
    }

    /**
     * sets a list of fields that are missing
     * @param array $missingFields
     * @return $this
     */
    public function setMissingFields(array $missingFields = [])
    {
        $this->missingFields = $missingFields;
        return $this;
    }

    public function getDefaultMessage()
    {
        return ExceptionDefaultMessages::REQUIRED_FIELD_MISSING_EXCEPTION;
    }
}
