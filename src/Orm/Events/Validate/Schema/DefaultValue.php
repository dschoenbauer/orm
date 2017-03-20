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
namespace DSchoenbauer\Orm\Events\Validate\Schema;

use DSchoenbauer\Orm\Entity\HasDefaultValuesInterface;
use DSchoenbauer\Orm\Enum\ModelEvents;
use DSchoenbauer\Orm\Events\Validate\AbstractValidate;

/**
 * Adds a default value to a data set at create time
 *
 * @author David Schoenbauer
 */
class DefaultValue extends AbstractValidate
{

    /**
     * provides an associative array that has a key of the field and a value
     * @param HasDefaultValuesInterface $entity
     * @return array
     * @since v1.0.0
     */
    public function getFields($entity)
    {
        return $entity->getDefaultValues();
    }

    /**
     * 
     * @return string
     */
    public function getTypeInterface()
    {
        return HasDefaultValuesInterface::class;
    }
    
    public function preExecuteCheck()
    {
        return is_array($params = $this->getParams()) &&
            array_key_exists('events', $params) &&
            is_array($params['events']) &&
            in_array(ModelEvents::CREATE, $params['events']);
    }

    /**
     * 
     * @param array $data
     * @param array $fields
     */
    public function validate(array $data, array $fields)
    {
        $this->getModel()->setData(array_merge($fields, $data));
        return true;
    }
}
